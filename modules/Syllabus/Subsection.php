<?php

/**
 * Subsection active record base implementation.
 * Subsections belong to parent Sections.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_Subsection extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_subsections',
            '__pk' => ['id'],
            
            'id' => 'int',
            'title' => 'string',
            'description' => 'string',        
            'sectionVersionId' => ['int', 'nativeName' => 'section_version_id'],
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],
            'isAnchored' => ['bool', 'nativeName' => 'is_anchored'],
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],

            'parent' => ['1:1', 'to' => 'Syllabus_Syllabus_Section', 'keyMap' => ['section_version_id' => 'id']],
        ];
    }
}
