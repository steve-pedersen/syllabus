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
        ];
    }

    public function getAuthorizationId () { return "at:syllabus:organizations/Department/{$this->id}"; }

    public function getOrgType () { return 'Department'; }

    public function setAbbreviation ($abbrev)
    {
        if (($pos = strrpos($abbrev, ' - ')) !== false)
        {
            $abbrev = substr($abbrev, $pos+3);
        }

        $this->_assign('abbreviation', $abbrev);
    }

    public function getUserRoles (Bss_AuthN_Account $user)
    {
        $authZ = $this->getApplication()->authorizationManager;
        $userRoles = [
            'member'                => $authZ->hasPermission($user, 'view org templates', $this),
            'communicator'          => $authZ->hasPermission($user, 'create communications', $this),
            'creator'               => $authZ->hasPermission($user, 'create org templates', $this),
            'repository_manager'    => $authZ->hasPermission($user, 'manage repository', $this),
            'moderator'             => $authZ->hasPermission($user, 'manage submitted syllabi', $this),
            'manager'               => $authZ->hasPermission($user, 'manage org', $this)
        ];

        return $userRoles;
    }


    public function getCommunicators ($reload=false)
    {
        if ($reload)
        {
            $userAzids = $authZ->getSubjectsWhoCan('create communications', $this);
            $this->_communicators = $this->getSchema('Bss_AuthN_Account')->getByAzids($userAzids);
        }
        return $this->_communicators; 
    }

    public function getCreators ($reload=false)
    {
        if ($reload)
        {
            $userAzids = $authZ->getSubjectsWhoCan('create org templates', $this);
            $this->_creators = $this->getSchema('Bss_AuthN_Account')->getByAzids($userAzids);
        }
        return $this->_creators;
    }

    public function getRepositoryManagers ($reload=false)
    {
        if ($reload)
        {
            $userAzids = $authZ->getSubjectsWhoCan('manage repository', $this);
            $this->_repositoryManagers = $this->getSchema('Bss_AuthN_Account')->getByAzids($userAzids);
        }
        return $this->_repositoryManagers;
    }

    public function getModerators ($reload=false)
    {
        if ($reload)
        {
            $userAzids = $authZ->getSubjectsWhoCan('manage submitted syllabi', $this);
            $this->_moderators = $this->getSchema('Bss_AuthN_Account')->getByAzids($userAzids);
        }
        return $this->_moderators;
    }


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

    public function addCommunicators ($users=[])
    {
        $authZ = $this->getApplication()->authorizationManager;
        $users = is_array($users) ? $users : [$users];
        foreach ($users as $user)
        {
            $authZ->grantPermission($user, 'create communications', $this);
        }
        $authZ->updateCache();

    }

    public function addCreators ($users=[])
    {
        $authZ = $this->getApplication()->authorizationManager;
        $users = is_array($users) ? $users : [$users];
        foreach ($users as $user)
        {
            $authZ->grantPermission($user, 'edit org templates', $this);
            $authZ->grantPermission($user, 'comment org templates', $this);
            $authZ->grantPermission($user, 'delete own org templates', $this);
        }
        $authZ->updateCache();
    }

    public function addRepositoryManagers ($users=[])
    {
        $authZ = $this->getApplication()->authorizationManager;
        $users = is_array($users) ? $users : [$users];
        foreach ($users as $user)
        {
            $authZ->grantPermission($user, 'view private repository', $this);
            $authZ->grantPermission($user, 'manage repository', $this);
        }
        $authZ->updateCache();  
    }

    public function addModerators ($users=[])
    {
        $authZ = $this->getApplication()->authorizationManager;
        $users = is_array($users) ? $users : [$users];
        foreach ($users as $user)
        {
            $authZ->grantPermission($user, 'create communications', $this);
            $authZ->grantPermission($user, 'manage submitted syllabi', $this);
            $authZ->grantPermission($user, 'comment submitted syllabi', $this);
            $authZ->grantPermission($user, 'comment org templates', $this);
            $authZ->grantPermission($user, 'delete own org templates', $this);
            // edit org templates ??
        }
        $authZ->updateCache();    
    }

    public function addManagers ($users=[])
    {
        $authZ = $this->getApplication()->authorizationManager;
        $users = is_array($users) ? $users : [$users];
        foreach ($users as $user)
        {
            $authZ->grantPermission($user, 'delete org templates', $this);
            $authZ->grantPermission($user, 'manage org users', $this);
            $authZ->grantPermission($user, 'manage repository', $this);
            $authZ->grantPermission($user, 'manage org', $this);
        }
        $authZ->updateCache();
    }
}
