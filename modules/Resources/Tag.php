<?php

/**
 * Adds tags to campus resources
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Resources_Tag extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_campus_resources_tags',
            '__pk' => ['id'],
            
            'id' => 'int',   
            'name' => 'string',

            'resources' => ['N:M',
                'to' => 'Syllabus_Syllabus_CampusResource',
                'via' => 'syllabus_campus_resources_tags_map',
                'fromPrefix' => 'tags',
                'toPrefix' => 'campus_resources',
            ],
        ];
    }
}
