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
        return array(
            '__type' => 'syllabus_subsections',
            '__pk' => array('id'),
            
            'id' => 'int',
            'title' => 'string',
            'description' => 'string',        
            'sectionVersionId' => array('int', 'nativeName' => 'section_version_id'),      
            'createdDate' => array('datetime', 'nativeName' => 'created_date'),
            'modifiedDate' => array('datetime', 'nativeName' => 'modified_date'),
            'isAnchored' => array('bool', 'nativeName' => 'is_anchored'),
            'sortOrder' => array('int', 'nativeName' => 'sort_order'),

            'parent' => array('1:1', 'to' => 'Syllabus_Syllabus_Section', 'keyMap' => array('section_version_id' => 'id')),
        );
    }
}
