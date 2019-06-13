<?php

/**
 */
class Syllabus_Organizations_Group extends Syllabus_Organizations_AbstractOrganization
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_groups',
            '__pk' => ['id'],
            '__azidPrefix' => 'at:syllabus:organizations/Group/',
            
            'id' => 'int',
            'name' => 'string',
            'abbreviation' => 'string',
            'displayName' => ['string', 'nativeName' => 'display_name'],
            'description' => 'string',
            
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],

            // 'parent' => ['1:1', 'to' => 'Syllabus_Organizations_Group'],
            // 'children' => ['1:N', 'to' => 'Syllabus_Organizations_Group', 'reverseOf' => 'parent', 'orderBy' => ['id']],
        ];
    }
}
