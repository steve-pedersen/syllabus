<?php

/**
 */
class Syllabus_AcademicOrganizations_Department extends Syllabus_Organizations_AbstractOrganization
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_departments',
            '__pk' => ['id'],
            '__azidPrefix' => 'at:syllabus:organizations/Department/',
            
            'id'    => 'int',
            'name'  => 'string',
            'abbreviation'  => 'string',
            'description'   => 'string',
            'displayName'   => ['string', 'nativeName' => 'display_name'],
            'externalKey'   => ['string', 'nativeName' => 'external_key'],        
            'createdDate'   => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate'  => ['datetime', 'nativeName' => 'modified_date'],

            'parent'    => ['1:1', 'to' => 'Syllabus_AcademicOrganizations_College', 'keyMap' => ['college_id' => 'id']],
            'children'  => ['1:N', 'to' => 'Syllabus_AcademicOrganizations_Department', 'reverseOf' => 'parent', 'orderBy' => ['id']],
            'college'   => ['1:1', 'to' => 'Syllabus_AcademicOrganizations_College', 'keyMap' => ['college_id' => 'id']],
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