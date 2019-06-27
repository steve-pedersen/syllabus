<?php

/**
 * Activity section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Activities_Activity extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_activities_activities',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'name' => 'string',
            'value' => 'string',
            'description' => 'string',
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],

            'activitiesSection' => ['1:1', 'to' => 'Syllabus_Activities_Activities', 'keyMap' => ['activities_id' => 'id']],
        ];
    }
}
