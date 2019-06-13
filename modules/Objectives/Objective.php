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
            '__type' => 'syllabus_objectives',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'title' => 'string',
            'description' => 'string',
        ];
    }
}
