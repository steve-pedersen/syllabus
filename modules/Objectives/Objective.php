<?php

/**
 * Objective section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Objectives_Objective extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_objectives_objectives',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'title' => 'string',
            'description' => 'string',
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],

            'objectivesSection' => ['1:1', 'to' => 'Syllabus_Objectives_Objectives', 'keyMap' => ['objectives_id' => 'id']],
        ];
    }
}
