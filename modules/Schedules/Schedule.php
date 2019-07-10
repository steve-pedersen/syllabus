<?php

/**
 * Schedule section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Schedules_Schedule extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_schedules_schedules',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'column1' => 'string',
            'column2' => 'string',
            'column3' => 'string',
            'column4' => 'string',
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],

            'parent' => ['1:1', 'to' => 'Syllabus_Schedules_Schedules', 'keyMap' => ['schedules_id' => 'id']],
        ];
    }
}
