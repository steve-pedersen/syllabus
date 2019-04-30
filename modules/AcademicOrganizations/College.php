<?php

/**
 */
class Syllabus_AcademicOrganizations_College extends Syllabus_Organizations_AbstractOrganization
{
    public static function SchemaInfo ()
    {
        return array(
            '__type' => 'syllabus_colleges',
            '__pk' => array('id'),
            '__azidPrefix' => 'at:syllabus:organizations/College/',
            
            'id' => 'int',
            'name' => 'string',
            'abbreviation' => 'string',
            'displayName' => array('string', 'nativeName' => 'display_name'),
            'description' => 'string',
            
            'createdDate' => array('datetime', 'nativeName' => 'created_date'),
            'modifiedDate' => array('datetime', 'nativeName' => 'modified_date'),

            // 'parent' => array('1:1', 'to' => 'Syllabus_AcademicOrganizations_College'),
            // 'children' => array('1:N', 'to' => 'Syllabus_AcademicOrganizations_Department', 'reverseOf' => 'parent', 'orderBy' => array('id')),
        );
    }

    public function getAuthorizationId () { return "at:syllabus:organizations/College/{$this->id}"; }

    public function getOrgType () { return 'College'; }

    public function getUserRoles (Bss_AuthN_Account $user)
    {

    }

    public function getCommunicators ($reload=false) {}
    public function getCreators ($reload=false) {}
    public function getRepositoryManagers ($reload=false) {}
    public function getModerators ($reload=false) {}

    public function addMembers ($users=[]) {}
    public function addCommunicators ($users=[]) {}
    public function addCreators ($users=[]) {}
    public function addRepositoryManagers ($users=[]) {}
    public function addModerators ($users=[]) {}
    public function addManagers ($users=[]) {}
}
