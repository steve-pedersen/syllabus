<?php

/**
 * The actual title/description policy items
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Policies_Policy extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_policies_policies',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'title' => 'string',
            'description' => 'string',
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],

            'policiesSection' => ['1:1', 'to' => 'Syllabus_Policies_Policies', 'keyMap' => ['policies_id' => 'id']],
        ];
    }
}
