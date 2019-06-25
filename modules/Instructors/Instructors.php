<?php

/**
 * Instructor section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Instructors_Instructors extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_instructors',
            '__pk' => ['id'],
            
            'id' => 'int',

            'instructors' => ['1:N', 'to' => 'Syllabus_Instructors_Instructor', 'reverseOf' => 'instructorsSection', 'orderBy' => ['+sortOrder']],
        ];
    }

    public function getInstructors ()
    {
        $instructors = [];
        foreach ($this->_fetch('instructors') as $obj)
        {
            $instructors[] = $obj;
        }    
        return !empty($instructors) ? $instructors : null;
    }

    public function processEdit ($request) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';
        if (isset($data['section']) && isset($data['section']['real']))
        {
            $this->save();
            $schema = $this->getSchema('Syllabus_Instructors_Instructor');
            foreach ($data['section']['real'] as $id => $instructor)
            {
                if ($this->isNotWhiteSpaceOnly($instructor, 'name'))
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
                        $obj->absorbData($instructor);
                        $obj->instructors_id = $this->id;
                        $obj->save();
                    }   
                }
                else
                {
                    $errorMsg = 'Any instructors with empty descriptions were not saved.';
                }
            }
        }

        return $errorMsg;
    }
}
