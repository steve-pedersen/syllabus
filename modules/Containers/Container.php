<?php

/**
 * Container section type active record. 
 * This type groups together other sections in a syllabus, 
 * like a Policy Container to group all policies together.
 * Especially useful for the ordering of sections when inheriting
 * sections from templates.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Containers_Container extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_containers',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'title' => 'string',
            'description' => 'string',
        ];
    }
}
