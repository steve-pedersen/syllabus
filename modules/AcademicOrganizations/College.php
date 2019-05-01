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
        ];
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

    public function addMembers ($users=[])
    {
        $authZ = $this->getApplication()->authorizationManager;
        $users = is_array($users) ? $users : [$users];
        foreach ($users as $user)
        {
            $authZ->grantPermission($user, 'view org', $this);
            $authZ->grantPermission($user, 'view org templates', $this);
            $authZ->grantPermission($user, 'view org members', $this);
            $authZ->grantPermission($user, 'view communications', $this);
            $authZ->grantPermission($user, 'view public repository', $this);
        }
        $authZ->updateCache();
    }

    public function addCommunicators ($users=[]) {}
    public function addCreators ($users=[]) {}
    public function addRepositoryManagers ($users=[]) {}
    public function addModerators ($users=[]) {}
    public function addManagers ($users=[]) {}
}
