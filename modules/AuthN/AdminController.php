<?php

/**
 * Administrate accounts, roles, and access levels.
 * 
 * @author      Daniel A. Koepke (dkoepke@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_AuthN_AdminController extends Syllabus_Master_Controller
{
    public static function getRouteMap ()
    {
        return [
            'admin/accounts' => ['callback' => 'listAccounts'],
            'admin/accounts/:id' => ['callback' => 'editAccount', ':id' => '([0-9]+|new)'],
            'admin/roles' => ['callback' => 'listRoles'],
			'admin/roles/all' => ['callback' => 'listRoles', 'showAll' => true],
            'admin/roles/:id' => ['callback' => 'editRole', ':id' => '([0-9]+|new)'],
            'admin/roles/:id/delete' => ['callback' => 'deleteRole', ':id' => '[0-9]+'],
            'admin/levels/:id' => ['callback' => 'editAccessLevel', ':id' => '([0-9]+|new)'],
			'admin/levels/:id/delete' => ['callback' => 'deleteAccessLevel', ':id' => '[0-9]+'],
        ];
    }

    public function beforeCallback ($callback)
    {
        parent::beforeCallback($callback);
        $this->requirePermission('admin');
        $this->template->privilegedOrganizations = $this->getPrivelegedUserOrganizations();
    }

    /**
     * Show a paginated list of accounts.
     */
    public function listAccounts ()
    {
        $this->setPageTitle('Administrate accounts');
        $accounts = $this->schema('Bss_AuthN_Account');
        
        if ($this->getPostCommand() == 'become' && $this->request->wasPostedByUser())
        {
            $commandMap = $this->request->getPostParameter('command');
            $accountIds = array_keys($commandMap['become']);
            $accountToBecome = $accounts->get($accountIds[0]);
            $returnTo = $this->request->getQueryParameter('returnTo', $this->request->getFullRequestedUri());
    
            $userContext = $this->getUserContext();
            $userContext->becomeAccount($accountToBecome, $returnTo);
        }
        
        $page = $this->request->getQueryParameter('page', 1);
        $limit = $this->request->getQueryParameter('limit', 20);
        $searchQuery = $this->request->getQueryParameter('sq');
        $sortBy = $this->request->getQueryParameter('sort', 'name');
        $sortDir = $this->request->getQueryParameter('dir', 'asc');
        $dirPrefix = ($sortDir == 'asc' ? '+' : '-');
        
        $page = max(1, $page);
        $offset = ($page-1) * $limit;
        
        $optionMap = [];
        
        if ($limit)
        {
            $optionMap['limit'] = $limit;
            
            if ($offset)
            {
                $optionMap['offset'] = $offset;
            }
        }
        
        switch ($sortBy)
        {
            case 'name':
                $optionMap['orderBy'] = array($dirPrefix . 'lastName', $dirPrefix . 'firstName', $dirPrefix . 'id');
                break;
            
            case 'email':
                $optionMap['orderBy'] = array($dirPrefix . 'email', $dirPrefix . 'id');
                break;
            
            case 'uni':
                $optionMap['orderBy'] = array($dirPrefix . 'university.name', $dirPrefix . 'id');
                break;
            
            case 'login':
                $optionMap['orderBy'] = array($dirPrefix . 'lastLoginDate', $dirPrefix . 'lastName', $dirPrefix . 'firstName', $dirPrefix . 'id');
                break;
        }
        
        $condition = null;
        
        if (!empty($searchQuery))
        {
            $pattern = '%' . strtolower($searchQuery) . '%';
            if (strpos($pattern, ' ') !== false)
            {
                $patternParts = explode(' ', $pattern);

                $condition = 
                    $accounts->firstName->lower()->like($patternParts[0])->orIf(
                        $accounts->lastName->lower()->like($patternParts[0]),
                        $accounts->middleName->lower()->like($patternParts[0]),
                        $accounts->emailAddress->lower()->like($patternParts[0]),
                        $accounts->username->lower()->like($patternParts[0])
                    );
                $condition = $condition->andIf(
                    $accounts->firstName->lower()->like($patternParts[1])->orIf(
                        $accounts->lastName->lower()->like($patternParts[1]),
                        $accounts->middleName->lower()->like($patternParts[1]),
                        $accounts->emailAddress->lower()->like($patternParts[1]),
                        $accounts->username->lower()->like($patternParts[1])
                    )
                );
                if (count($patternParts) > 2)
                {
                    $condition = $condition->andIf(
                        $accounts->firstName->lower()->like($patternParts[2])->orIf(
                            $accounts->lastName->lower()->like($patternParts[2]),
                            $accounts->middleName->lower()->like($patternParts[2]),
                            $accounts->emailAddress->lower()->like($patternParts[2]),
                            $accounts->username->lower()->like($patternParts[2])
                        )
                    );                    
                }
            }
            else
            {
                $condition = 
                    $accounts->firstName->lower()->like($pattern)->orIf(
                        $accounts->lastName->lower()->like($pattern),
                        $accounts->middleName->lower()->like($pattern),
                        $accounts->emailAddress->lower()->like($pattern),
                        $accounts->username->lower()->like($pattern)
                    );
            }
            // $pattern = str_replace(' ', '%', $pattern);

        }
        
        $totalAccounts = $accounts->count($condition);
        $pageCount = ceil($totalAccounts / $limit);
        
        $this->template->pagesAroundCurrent = $this->getPagesAroundCurrent($page, $pageCount);
        
        $accountList = $accounts->find($condition, $optionMap);
        
        $this->template->searchQuery = $searchQuery;
        $this->template->totalAccounts = $totalAccounts;
        $this->template->pageCount = $pageCount;
        $this->template->currentPage = $page;
        $this->template->accountList = $accountList;
        $this->template->sortBy = $sortBy;
        $this->template->dir = $sortDir;
        $this->template->oppositeDir = ($sortDir == 'asc' ? 'desc' : 'asc');
        $this->template->limit = $limit;
    }

    public function editAccount ()
    {
        $viewer = $this->requireLogin();
        $id = $this->getRouteVariable('id');
        $accounts = $this->schema('Bss_AuthN_Account');
        $returnTo = $this->request->getQueryParameter('returnTo', 'admin/accounts');
        
        if ($id == 'new')
        {
            $this->setPageTitle('New account');
            $account = $accounts->createInstance();
            $newAccount = true;
        }
        else
        {
            if (!($account = $accounts->get($id)))
            {
                $this->notFound();
            }
            $newAccount = false;
            $this->setPageTitle('Edit ' . $account->displayName);
        }

        $roles = $this->schema('Syllabus_AuthN_Role');
        $roleList = $roles->find($roles->isSystemRole->equals(true), array('orderBy' => '+name'));
        
        if ($this->request->wasPostedByUser())
        {
            if ($account->handleSettings($this->request, true, $roleList))
            {
                if ($id == 'new')
                {
                    $account->source = 'admin';
                    $account->createdDate = new DateTime;
                }
                
                $account->save();
                $this->response->redirect($returnTo);
            }
            else
            {
                $this->template->errorMap = $account->getValidationMessages();
            }
        }

        $this->template->newAccount = $newAccount;
        $this->template->account = $account;
        $this->template->roleList = $roleList;
        $this->template->returnTo = $returnTo;
    }

    private function getQueryString ($merge = null)
    {
		$qsa = array(
            'page' => $this->request->getQueryParameter('page', 1),
            'limit' => $this->request->getQueryParameter('limit', 20),
            'sq' => $this->request->getQueryParameter('sq'),
            'sort' => $this->request->getQueryParameter('sort', 'name'),
            'dir' => $this->request->getQueryParameter('dir', 'asc'),
        );
		
		if ($merge)
		{
			foreach ($merge as $k => $v)
			{
				if ($v !== null)
				{
					$qsa[$k] = $v;
				}
				elseif (isset($qsa[$k]))
				{
					unset($qsa[$k]);
				}
			}
		}
		
		if (!empty($qsa))
		{
			$qsaString = '';
			$first = true;
			
			foreach ($qsa as $k => $v)
			{
				$qsaString .= ($first ? '?' : '&') . urlencode($k) . '=' . urlencode($v);
				$first = false;
			}
			
			return $qsaString;
		}
		
		return '';
    }
    
    private function getPagesAroundCurrent ($currentPage, $pageCount)
    {
		$pageList = [];
		
        if ($pageCount > 0)
        {
    		$minPage = max(1, $currentPage - 5);
    		$maxPage = min($pageCount, $currentPage + 5);
    		
    		if ($pageCount != 1)
    		{
    			$pageList[] = array(
    				'page' => $currentPage-1,
    				'display' => 'Previous',
    				'disabled' => ($currentPage == 1),
    				'href' => 'admin/accounts' . $this->getQueryString(array('page' => $currentPage-1)),
    			);
    		}
    		
    		if ($minPage > 1)
    		{
    			$pageList[] = array(
    				'page' => 1,
    				'display' => 'First',
    				'current' => false,
    				'href' => 'admin/accounts' . $this->getQueryString(array('page' => 1)),
    			);
    			
    			if ($minPage > 2)
    			{
    				$pageList[] = array('separator' => true);
    			}
    		}
    		
    		for ($page = $minPage; $page <= $maxPage; $page++)
    		{
    			$current = ($page == $currentPage);
    			
    			$pageList[] = array(
    				'page' => $page,
    				'display' => $page,
    				'current' => $current,
    				'href' => 'admin/accounts' . $this->getQueryString(array('page' => $page)),
    			);
    		}
    		
    		if ($maxPage < $pageCount)
    		{
    			if ($maxPage+1 < $pageCount)
    			{
    				$pageList[] = array('separator' => true);
    			}
    			
    			$pageList[] = array(
    				'page' => $pageCount,
    				'display' => 'Last',
    				'current' => false,
    				'href' => 'admin/accounts' . $this->getQueryString(array('page' => $pageCount)),
    			);
    		}
    		
    		if ($pageCount != 1)
    		{
    			$pageList[] = array(
    				'page' => $currentPage+1,
    				'display' => 'Next',
    				'disabled' => ($currentPage == $pageCount),
    				'href' => 'admin/accounts' . $this->getQueryString(array('page' => $currentPage+1)),
    			);
    		}
        }
		
		return $pageList;
    }
    
    /**
     */
    public function listRoles ()
    {
		$showAll = $this->getRouteVariable('showAll');
		
		$roles = $this->schema('Syllabus_AuthN_Role');
		$accessLevels = $this->schema('Syllabus_AuthN_AccessLevel');
		
		if ($showAll)
		{
			$this->template->showAll = true;
			$this->template->roleList = $roles->getAll(array('orderBy' => array('+name', '+id')));
		}
		else
		{
			$this->template->roleList = $roles->find($roles->isSystemRole->equals(true), array('orderBy' => array('+name', '+id')));
		}
        
		$this->setPageTitle('Roles and access levels');
        $this->template->accessLevelList = $accessLevels->getAll(array('orderBy' => array('+name', '+id')));
    }

	/**
	 */
	public function editRole ()
	{
		$id = $this->getRouteVariable('id');
		$roles = $this->schema('Syllabus_AuthN_Role');
		
		if ($id == 'new')
		{
			$role = $roles->createInstance();
			$this->setPageTitle('Add new role');
		}
		else
		{
			$role = $roles->get($id);
			
			if ($role == null)
			{
				$this->notFound(array(
					array('href' => 'admin/roles', 'text' => 'Roles and access levels'),
					array('href' => 'admin', 'text' => 'Admin dashboard'),
				));
			}
			
			$this->setPageTitle('Edit role &ldquo;' . htmlspecialchars($role->name) . '&rdquo;');
		}
		
		$authZ = $this->getAuthorizationManager();
		$accessLevels = $this->schema('Syllabus_AuthN_AccessLevel');
		$this->template->accessLevelList = $accessLevelList = $accessLevels->getAll(array('orderBy' => array('+name', '+id')));
		$this->template->taskDefinitionMap = $authZ->getDefinedTasks();
		$this->template->systemAzid = Bss_AuthZ_Manager::SYSTEM_ENTITY;
		
		if (($postCommand = $this->getPostCommand()))
		{
			// Either save or apply.
			$successful = $this->processSubmission($role, array('name', 'description', 'isSystemRole'));
			$role->save();
			$hash = null;
			
			// Add a task.
			$addTask = $this->request->getPostParameter('addTask');
			$addTarget = $this->request->getPostParameter('addTarget');
			
			if ($addTask && $addTarget)
			{
				if ($addTarget != 'system')
				{
					$addTarget = 'at:syllabus:authN/AccessLevel/' . $addTarget;
				}
				$authZ->grantPermission($role, $addTask, $addTarget);
				$hash = 'perms';
			}
			
			// Remove selected tasks.
			$selTaskMap = (array) $this->request->getPostParameter('task');
			
			foreach ($selTaskMap as $task => $entitySet)
			{
				if (is_array($entitySet))
				{
					foreach ($entitySet as $entityId => $nonce)
					{
						if ($entityId != 'system')
						{
							$entityId = 'at:syllabus:authN/AccessLevel/' . $entityId;
						}
                        
						$authZ->revokePermission($role, $task, $entityId);
						$hash = 'perms';
					}
				}
			}
			
			// TODO: IP assignments.
			
			if ($postCommand == 'apply' && ($id == 'new' || $hash))
			{
				$this->response->redirect('admin/roles/' . $role->id . ($hash ? '#' . $hash : ''));
			}
			elseif ($postCommand == 'save')
			{
				$this->response->redirect('admin/roles');
			}
		}
		
		if ($role->inDataSource)
		{
			$entityList = array(
				array('id' => 'system', 'name' => 'System', 'permissionList' => $authZ->getPermissions($role, Bss_AuthZ_Manager::SYSTEM_ENTITY)),
			);
			
			foreach ($accessLevelList as $accessLevel)
			{
				$entityList[] = array(
					'id' => $accessLevel->id,
					'name' => $accessLevel->name . ' access',
					'permissionList' => $authZ->getPermissions($role, $accessLevel),
				);
			}
			
			$this->template->entityList = $entityList;
			$this->template->authZ = $authZ;
		}
		
		$this->template->role = $role;
	}
    
    public function deleteRole ()
    {
        $id = $this->getRouteVariable('id');
        $roles = $this->schema('Syllabus_AuthN_Role');
        $role = $roles->get($id);
        
        if ($this->getPostCommand() && $this->request->wasPostedByUser())
        {
            // Delete the role from users -- we do this without loading the accounts.
            $role->getSchema()->accounts->remove($role);
			$this->getAuthorizationManager()->deprovision($role);
            
            // TODO: Allow reassigning users in this role to a new role.
            
            $role->delete();
            $this->response->redirect('admin/roles');
        }
        
        $this->template->role = $role;
    }

	public function editAccessLevel ()
	{
		$id = $this->getRouteVariable('id');
		$accessLevels = $this->schema('Syllabus_AuthN_AccessLevel');
		
		if ($id == 'new')
		{
			$accessLevel = $accessLevels->createInstance();
			$this->setPageTitle('Add access level');
		}
		elseif (($accessLevel = $accessLevels->get($id)) != null)
		{
			$this->setPageTitle('Edit access level &ldquo;' . htmlspecialchars($accessLevel->name) . '&rdquo;');
		}
		else
		{
			$this->notFound(array(
				array('href' => 'admin/roles', 'text' => 'Roles and access levels'),
				array('href' => 'admin', 'text' => 'Admin dashboard'),
			));
		}
		
		if ($this->getPostCommand() && $this->processSubmission($accessLevel, array('name', 'description')))
		{
			$accessLevel->save();
			$this->response->redirect('admin/roles');
		}
		
		$this->template->accessLevel = $accessLevel;
	}
	
	public function deleteAccessLevel ()
	{
		$id = $this->getRouteVariable('id');
		$accessLevel = $this->schema('Syllabus_AuthN_AccessLevel')->get($id);
		
		if ($accessLevel == null)
		{
			$this->notFound(array(
				array('href' => 'admin/roles', 'text' => 'Roles and access levels'),
				array('href' => 'admin', 'text' => 'Admin dashboard'),
			));
		}
		
		$this->setPageTitle('Delete access level &ldquo;' . htmlspecialchars($accessLevel->name) . '&rdquo;?');
		
		if ($this->getPostCommand() && $this->request->wasPostedByUser())
		{
			$this->getAuthorizationManager()->deprovision($accessLevel);
			$accessLevel->delete();
			$this->response->redirect('admin/roles');
		}
		
		$this->template->accessLevel = $accessLevel;
	}
}
