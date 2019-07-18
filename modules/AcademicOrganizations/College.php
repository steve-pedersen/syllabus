<?php

/**
 */
class Syllabus_AcademicOrganizations_College extends Syllabus_Organizations_AbstractOrganization
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_colleges',
            '__pk' => ['id'],
            '__azidPrefix' => 'at:syllabus:organizations/College/',
            
            'id' => 'int',
            'name' => 'string',
            'abbreviation' => 'string',
            'displayName' => ['string', 'nativeName' => 'display_name'],
            'description' => 'string',
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],

            'departments' => ['1:N', 'to' => 'Syllabus_AcademicOrganizations_Department', 'reverseOf' => 'parent', 'orderBy' => ['id']],
            'children' => ['1:N', 'to' => 'Syllabus_AcademicOrganizations_Department', 'reverseOf' => 'parent', 'orderBy' => ['id']],
        ];
    }

    public function setAbbreviation ($abbrev)
    {
        if (($pos = strrpos($abbrev, ' - ')) !== false)
        {
            $abbrev = substr($abbrev, $pos+3);
        }
        $this->_assign('abbreviation', $abbrev);
    }
}
