<?php

/**
 * Grades section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Grades_Grades extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_grades',
            '__pk' => ['id'],
            
            'id' => 'int',
            'columns' => 'int',
            'header1' => 'string',
            'header2' => 'string',
            'header3' => 'string',
            'additionalInformation' => ['string', 'nativeName' => 'additional_information'],

            'grades' => ['1:N', 'to' => 'Syllabus_Grades_Grade', 'reverseOf' => 'gradesSection', 'orderBy' => ['+sortOrder']],
        ];
    }

    public function getGrades ()
    {
        $grades = [];
        foreach ($this->_fetch('grades') as $obj)
        {
            $grades[] = $obj;
        }    
        return !empty($grades) ? $grades : null;
    }

    public function processEdit ($request) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';
        if (isset($data['section']) && isset($data['section']['real']))
        {
            $this->absorbData($data['section']['real']);
            $this->save();

            unset($data['section']['real']['columns']);
            unset($data['section']['real']['header1']);
            unset($data['section']['real']['header2']);
            unset($data['section']['real']['header3']);
            unset($data['section']['real']['additionalInformation']);

            $schema = $this->getSchema('Syllabus_Grades_Grade');
            foreach ($data['section']['real'] as $id => $grade)
            {
                if ($this->isNotWhiteSpaceOnly($grade, 'column1'))
                {
                    $obj = (!is_numeric($id)) ? $schema->createInstance() : $schema->get($id);
                    $save = true;
                    if ($obj->inDatasource)
                    {
                        if ($obj->id != $id)
                        {
                            $save = false;
                        }
                    }
                    if ($save)
                    {
                        $obj->absorbData($grade);
                        $obj->grades_id = $this->id;
                        $obj->save();
                    }   
                }
                else
                {
                    $errorMsg = 'Any rows with empty cells were not saved.';
                }
            }
        }

        return $errorMsg;
    }
}
