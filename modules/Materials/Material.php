<?php

/**
 * The actual materials
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Materials_Material extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_materials_materials',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'title' => 'string',
            'url' => 'string',
            'required' => 'bool',
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],

            'materialsSection' => ['1:1', 'to' => 'Syllabus_Materials_Materials', 'keyMap' => ['materials_id' => 'id']],
        ];
    }
}
