<?php

/**
 */
abstract class Syllabus_Organizations_BaseController extends Syllabus_Master_Controller
{


	abstract public function getOrganization ($id=null);

    public function dashboard ()
    {
        
        $organization = $this->getOrganization($this->getRouteVariable('oid'));
        $this->template->organization = $organization;
        $this->buildHeader('partial:_header.html.tpl', 'Dashboard', $organization->name, '');
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('organizations', 'My Organizations');
        $this->addBreadcrumb($organization->routeName, ucfirst($organization->routeName));
    }

    public function listTemplates ()
    {
        $organization = $this->getOrganization($this->getRouteVariable('oid'));
        $this->template->organization = $organization;
        $this->buildHeader('partial:_header.html.tpl', 'Templates', $organization->name, '');
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('organizations', 'My Organizations');
        $this->addBreadcrumb($organization->routeName, ucfirst($organization->routeName));

        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');

        $this->template->templates = $syllabi->find(
            $syllabi->templateAuthorizationId->equals($organization->templateAuthorizationId),
            ['orderBy' => '-createdDate']
        );
    }

    public function myOrganizations ()
    {
        $this->buildHeader('partial:_header.edit.html.tpl', 'My Organizations', '', '');
        $viewer = $this->requireLogin();
        $departmentSchema = $this->schema('Syllabus_AcademicOrganizations_Department');
        $collegeSchema = $this->schema('Syllabus_AcademicOrganizations_College');
        // $groupSchema = $this->schema('Syllabus_Organizations_Group');
        $organizations = [];

        if ($this->hasPermission('admin'))
        {
            $organizations['colleges'] = $collegeSchema->getAll() ?? [];
            $organizations['departments'] = $departmentSchema->getAll() ?? [];
        }
        elseif ($viewer->classDataUser)
        {
            $authZ = $this->getAuthorizationManager();
            $azids = $authZ->getObjectsForWhich($viewer, 'view org templates');
            $organizations['colleges'] = $collegeSchema->getByAzids($azids) ?? [];
            $organizations['departments'] = $departmentSchema->getByAzids($azids) ?? [];
        }

        $this->template->allOrganizations = $organizations;
    }

    public function listOrganizations ()
    {
        // $this->buildHeader('partial:_header.edit.html.tpl', $this->organization->organizationType.'s', '', '');
        $viewer = $this->requireLogin();
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('organizations', 'My Organizations');
        $organizations = [];

        if ($this->hasPermission('admin'))
        {
            $organizations = $this->organizationSchema->getAll() ?? [];
        }
        else
        {
            $authZ = $this->getAuthorizationManager();
            $azids = $authZ->getObjectsForWhich($viewer, 'view org templates');
            $organizations = $this->organizationSchema->getByAzids($azids) ?? [];
        }

        $this->template->organizations = $organizations;    
    }



    public function manageOrganization ()
    {
        $viewer = $this->requireLogin();
        $organization = $this->helper('activeRecord')->fromRoute($this->organizationSchema, 'oid');
        $organization->requireRole('manager', $this);
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('organizations', 'My Organizations');
        $this->addBreadcrumb($organization->routeName, ucfirst($organization->routeName));
        $this->addBreadcrumb($organization->routeName .'/'. $organization->id, $organization->name);
    }

    public function manageUsers ()
    {
        $viewer = $this->requireLogin();
        $organization = $this->getOrganization($this->getRouteVariable('oid'));
        $organization->requireRole('manager', $this);
        $this->buildHeader('partial:_header.html.tpl', 'Manage Users', $organization->name, '');
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('organizations', 'My Organizations');
        $this->addBreadcrumb($organization->routeName, ucfirst($organization->routeName));
        $this->addBreadcrumb($organization->routeName .'/'. $organization->id, $organization->name);

        $accounts = $this->schema('Bss_AuthN_Account');

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
            $condition = 
                $accounts->firstName->lower()->like($pattern)->orIf(
                    $accounts->lastName->lower()->like($pattern),
                    $accounts->middleName->lower()->like($pattern),
                    $accounts->emailAddress->lower()->like($pattern)
                );
        }
        
        $totalAccounts = $accounts->count($condition);
        $pageCount = ceil($totalAccounts / $limit);
        
        $this->template->pagesAroundCurrent = $this->getPagesAroundCurrent($page, $pageCount, $organization);
        
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

        $this->template->organization = $organization;
    }

    public function editUser ()
    {
        $viewer = $this->requireLogin();
        $organization = $this->getOrganization($this->getRouteVariable('oid'));
        $organization->requireRole('manager', $this);
        $user = $this->requireExists($this->schema('Bss_AuthN_Account')->get($this->getRouteVariable('uid')));
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('organizations', 'My Organizations');
        $this->addBreadcrumb($organization->routeName, ucfirst($organization->routeName));
        $this->addBreadcrumb($organization->routeName.'/'.$organization->id, $organization->name);
        $this->addBreadcrumb($organization->routeName.'/'.$organization->id.'/users', 'Users');

        $this->buildHeader('partial:_header.html.tpl', 'Manage User', $user->fullName, $user->emailAddress . ' ' . $user->username);
        $returnTo = $this->request->getQueryParameter('returnTo');

        if ($this->request->wasPostedByUser())
        {
            switch ($this->getPostCommand()) {
                
                case 'save':
                    $currentRoles = $organization->getUserRoles($user);
                    $rolesData = $this->request->getPostParameter('roles');

                    foreach ($currentRoles as $role => $userHasRole)
                    {
                        if (isset($rolesData[$role]) && !$userHasRole)
                        {
                            $organization->grantUsersRole($user, $role);
                        }
                        elseif (!isset($rolesData[$role]) && $userHasRole)
                        {
                            // TODO: allow Groups to revoke member role
                            if ($role !== 'member')
                            {
                                $organization->revokeUsersRole($user, $role);
                            }
                        }
                    }

                    $this->flash('User settings have been saved.');
                    $this->response->redirect($returnTo);

                    break;
                case 'remove';
                    // TODO: only for Groups - revoke all roles for the Group
                    break;
                default:
                    break;
            }
        }

        $this->template->canRemove = false;
        $this->template->returnTo = $returnTo;
        $this->template->roles = $organization::$RoleDisplayNames;
        $this->template->account = $user;
        $this->template->organization = $organization;
    }

    private function getPagesAroundCurrent ($currentPage, $pageCount, $organization)
    {
        $pageList = [];
        $urlBase = $organization->routeName .'/'. $organization->id .'/users';
        
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
                    'href' => $urlBase . $this->getQueryString(array('page' => $currentPage-1)),
                );
            }
            
            if ($minPage > 1)
            {
                $pageList[] = array(
                    'page' => 1,
                    'display' => 'First',
                    'current' => false,
                    'href' => $urlBase . $this->getQueryString(array('page' => 1)),
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
                    'href' => $urlBase . $this->getQueryString(array('page' => $page)),
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
                    'href' => $urlBase . $this->getQueryString(array('page' => $pageCount)),
                );
            }
            
            if ($pageCount != 1)
            {
                $pageList[] = array(
                    'page' => $currentPage+1,
                    'display' => 'Next',
                    'disabled' => ($currentPage == $pageCount),
                    'href' => $urlBase . $this->getQueryString(array('page' => $currentPage+1)),
                );
            }
        }
        
        return $pageList;
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

}






