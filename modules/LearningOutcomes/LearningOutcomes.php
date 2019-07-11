<?php

/**
 * LearningOutcomes section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_LearningOutcomes_LearningOutcomes extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_learning_outcomes',
            '__pk' => ['id'],
            
            'id' => 'int',
            'columns' => 'int',
            'header1' => 'string',
            'header2' => 'string',
            'header3' => 'string',
            'additionalInformation' => ['string', 'nativeName' => 'additional_information'],

            'learningOutcomes' => ['1:N', 'to' => 'Syllabus_LearningOutcomes_LearningOutcome', 'reverseOf' => 'parent', 'orderBy' => ['+sortOrder']],
        ];
    }

    public function getLearningOutcomes ()
    {
        $learningOutcomes = [];
        foreach ($this->_fetch('learningOutcomes') as $obj)
        {
            $learningOutcomes[] = $obj;
        }    
        return !empty($learningOutcomes) ? $learningOutcomes : null;
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

            $schema = $this->getSchema('Syllabus_LearningOutcomes_LearningOutcome');
            foreach ($data['section']['real'] as $id => $learningOutcome)
            {
                if ($this->isNotWhiteSpaceOnly($learningOutcome, 'column1'))
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
                        $obj->absorbData($learningOutcome);
                        $obj->learning_outcomes_id = $this->id;
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
