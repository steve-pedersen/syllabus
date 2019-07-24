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
            $data = $data['section']['real'];
            $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
            $this->absorbData($data);
            $this->header1 = isset($data['header1']) ? strip_tags(trim($data['header1'])) : '';
            $this->header2 = isset($data['header2']) ? strip_tags(trim($data['header2'])) : '';
            $this->header3 = isset($data['header3']) ? strip_tags(trim($data['header3'])) : '';
            $this->additionalInformation = $htmlSanitizer->sanitize(trim($data['additionalInformation']));
            $this->save();

            unset($data['columns']);
            unset($data['header1']);
            unset($data['header2']);
            unset($data['header3']);
            unset($data['additionalInformation']);

            $schema = $this->getSchema('Syllabus_LearningOutcomes_LearningOutcome');
            foreach ($data as $id => $learningOutcome)
            {
                if ($this->isNotWhiteSpaceOnly($learningOutcome, 'column1') || 
                    $this->isNotWhiteSpaceOnly($learningOutcome, 'column2') ||
                    $this->isNotWhiteSpaceOnly($learningOutcome, 'column3'))
                {
                    // $obj = (!is_numeric($id)) ? $schema->createInstance() : $schema->get($id);
                    // $save = true;
                    // if ($obj->inDatasource)
                    // {
                    //     if ($obj->id != $id)
                    //     {
                    //         $save = false;
                    //     }
                    // }
                    $save = true;
                    $obj = $schema->createInstance();
                    if ($save)
                    {
                        $obj->absorbData($learningOutcome);
                        $obj->column1 = $htmlSanitizer->sanitize(trim($learningOutcome['column1']));
                        $obj->column2 = $htmlSanitizer->sanitize(trim($learningOutcome['column2']));
                        $obj->column3 = $htmlSanitizer->sanitize(trim($learningOutcome['column3']));
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
