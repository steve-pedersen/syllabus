<?php

/**
 * A class to handle the connections and shared properties among College, Dept., and Group models.
 * Each concrete organization may override the permission sets that define their user's roles.
 *
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   copyright (c) San Francisco State University
 */
abstract class Syllabus_Organizations_AbstractOrganization extends Bss_ActiveRecord_BaseWithAuthorization
{
    private $_members;
    private $_communicators;
    private $_creators;
    private $_repositoryManagers;
    private $_moderators;
    private $_managers;

    /* TaskDefinition sets for each user role. */ 
    public static $RolePermissions = [
        'member' => [
            'view org',
            'view org templates',
            'view org members',
            'view communications',
            'view public repository',
        ],
        'communicator' => [
            'create communications',
        ],
        'creator' => [
            'edit org templates',
            'create org templates',
            'comment org templates',
            'delete own org templates',
        ],
        'repository_manager' => [
            'view private repository',
            'manage repository',
        ],
        'moderator' => [
            'create communications',
            'manage submitted syllabi',
            'comment submitted syllabi',
            'comment org templates',
            'delete own org templates',
        ],
        'manager' => [
            'delete org templates',
            'manage org users',
            'manage repository',
            'manage org',
        ],
    ];

    /**
     * These are particular TaskDefinitions that cascade down to define each role.
     * E.g., if a user doesn't have 'manage org' but has 'manage submitted syllabi'
     * then they are a Moderator.
     */ 
    public static $RoleDefinitions = [
        'member'                => 'view org templates',
        'communicator'          => 'create communications',
        'creator'               => 'create org templates',
        'repository_manager'    => 'view private repository',
        'moderator'             => 'manage submitted syllabi',
        'manager'               => 'manage org',
    ];
    public static $RoleDisplayNames = [
        'member'                => 'Member',
        'communicator'          => 'Communicator',
        'creator'               => 'Creator',
        'repository_manager'    => 'Repository Manager',
        'moderator'             => 'Moderator',
        'manager'               => 'Manager',
    ];
    public static $RoleDefaultHelpText = [
        'member' =>
            'Basic role which can view organization templates, communications, members, and public repositories.',
        'communicator' =>
            'Create and send communications to members.',
        'creator' =>
            'Create, edit, and comment on templates.',
        'repository_manager' =>
            'Manages public and private repositories.',
        'moderator' =>
            'Handles submitted syllabi and can communicate to members.',
        'manager' =>
            'Manages users, organization settings, repositories, and templates.',
    ];


    public function getAuthorizationId () 
    { 
        return "at:syllabus:organizations/{$this->organizationType}/{$this->id}";
    }

    public function getTemplateAuthorizationId ()
    {
        return $this->routeName . '/' . $this->id;
    }

    /**
     * @return object name of any concrete class (College, Department, Group)
     */  
    public function getOrganizationType ()
    {
        return substr(get_class($this), (strrpos(get_class($this), '_') + 1));
    }

    public function getRouteName ()
    {
        return strtolower($this->organizationType) . 's';
    }

    public function getOrganizationSchemaName () { return get_class($this); }

    // TODO: Make this text configurable
    public function getRoleHelpText ($role) { return self::$RoleDefaultHelpText[$role]; }

    /**
     * Grant an array of Bss_AuthN_Accounts all permissions needed for specified
     * role in an organization. Overrides need to be done by concrete organizations.
     */   
    public function grantUsersRole ($users, $role='member')
    {
        $authZ = $this->application->authorizationManager;
        $users = is_array($users) ? $users : [$users];
        foreach ($users as $user)
        {
            foreach (self::$RolePermissions[$role] as $permission)
            {
                $authZ->grantPermission($user, $permission, $this);
            }
        }
        $authZ->updateCache();
    }

    public function revokeUsersRole ($users, $role)
    {
        $authZ = $this->application->authorizationManager;
        $users = is_array($users) ? $users : [$users];
        foreach ($users as $user)
        {
            foreach (self::$RolePermissions[$role] as $permission)
            {
                $authZ->revokePermission($user, $permission, $this);
            }
        }
        $authZ->updateCache();
    }

    /**
     * @param $user - the Bss_AuthN_Account to check against. Defaults to current logged in user.
     * @param $display - will return key/value pairs of only the role display names the user has
     * @return array of role key => bool for if user has a defining permission for that role
     */  
    public function getUserRoles ($user=null, $display=false)
    {
        $user = $user ?? $this->application->userContext->account;
        $authZ = $this->application->authorizationManager;
        $userRoles = [
            'member' => $authZ->hasPermission($user, self::$RoleDefinitions['member'], $this),
            'communicator' => $authZ->hasPermission($user, self::$RoleDefinitions['communicator'], $this),
            'creator' => $authZ->hasPermission($user, self::$RoleDefinitions['creator'], $this),
            'repository_manager' => $authZ->hasPermission($user, self::$RoleDefinitions['repository_manager'], $this),
            'moderator' => $authZ->hasPermission($user, self::$RoleDefinitions['moderator'], $this),
            'manager' => $authZ->hasPermission($user, self::$RoleDefinitions['manager'], $this),
        ];
        if ($display)
        {
            foreach ($userRoles as $key => $val)
            {
                if (!$val)
                {
                    unset($userRoles[$key]);
                }
                else
                {
                    $userRoles[$key] = self::$RoleDisplayNames[$key];
                }
            }
        }

        return $userRoles;
    }

    public function userHasRole ($user, $role)
    {
        return $this->getUserRoles($user)[$role];
    }

    public function userIsMoreThanMember ($user=null)
    {
        $user = $user ?? $this->application->userContext->account;
        foreach ($this->getUserRoles($user) as $role => $hasRole)
        {
            if ($role !== 'member' && $hasRole)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Fetch all users of a particular role in an organization.
     *
     * @param $role should be one of 'role keys' such as 'member' or 'manager'
     * @return array of Bss_AuthN_Accounts
     */  
    public function getRoleUsers ($role, $reload=true)
    {
        $authZ = $this->application->authorizationManager;
        $roleKey = '_' . $role . 's';
        if ($reload)
        {
            $userAzids = $authZ->getSubjectsWhoCan(self::$RoleDefinitions[$role], $this);
            $this->$roleKey = $this->getSchema('Bss_AuthN_Account')->getByAzids($userAzids);
        }
        return $this->$roleKey;
    }

    public function requireRole ($role, $ctrl)
    {
        $ctrl->requirePermission(self::$RoleDefinitions[$role], $this);
    }

  //   public function getDashboardTemplate ()
  //   {
  //       return $this->getOrganizationTemplate('dashboard');
  //   }

  //   public function getManageUsersTemplate ()
  //   {
		// return $this->getOrganizationTemplate('manageUsers');
  //   }

  //   public function getOrganizationTemplate ($callback)
  //   {
  //       $template = new DivaTemplate;
  //       $template->setDefaultResourceDirectory(glue_path(dirname(__FILE__), 'resources'));
  //       $template->setTemplateFile($callback . '.html.tpl');
        
  //       return $template;
  //   }
}