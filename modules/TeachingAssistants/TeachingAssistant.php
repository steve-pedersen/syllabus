<?php

/**
 * TeachingAssistant section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_TeachingAssistants_TeachingAssistant extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_teaching_assistants_teaching_assistants',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'name' => 'string',
            'email' => 'string',
            'additionalInformation' => ['string', 'nativeName' => 'additional_information'],
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],

            'teachingAssistantsSection' => ['1:1', 'to' => 'Syllabus_TeachingAssistants_TeachingAssistants', 'keyMap' => ['teaching_assistants_id' => 'id']],
        ];
    }
}
