<?php

/**
 * Grade section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Grades_Grade extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_grades_grades',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'column1' => 'string',
            'column2' => 'string',
            'column3' => 'string',
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],

            'parent' => ['1:1', 'to' => 'Syllabus_Grades_Grades', 'keyMap' => ['grades_id' => 'id']],
        ];
    }
}
