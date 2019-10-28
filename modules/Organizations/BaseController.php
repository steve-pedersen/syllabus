<?php

/**
 */
abstract class Syllabus_Organizations_BaseController extends Syllabus_Master_Controller
{
    private $_syllabusId;
    private $_organization;
    private $_routeBase;

	abstract public function getOrganization ($id=null);

    // protected function beforeCallback ($callback)
    // {
    //     parent::beforeCallback($callback);
    // }

    protected function initialize ()
    {
        $this->requireLogin();
        $oid = $this->getRouteVariable('oid');
        if ($oid && $oid !== 'new')
        {
            $this->_syllabusId = $this->getRouteVariable('id');
            $this->_organization = $this->getOrganization($this->getRouteVariable('oid'));
            $this->_routeBase = $this->_organization->routeName . '/' .$this->_organization->id . '/';
            $this->template->clearBreadcrumbs();
            // $this->addBreadcrumb('organizations', 'My Organizations');
            $this->addBreadcrumb($this->_organization->routeName, ucfirst($this->_organization->routeName));
            $this->template->addBreadcrumb($this->_routeBase, $this->_organization->abbreviation . ' Home');
            $this->template->routeBase = $this->_routeBase;
            if ($this->getAccount())
            {
                $this->template->pManager = $this->_organization->userHasRole($this->getAccount(), 'manager');
            }
        }
    }

    public function dashboard ()
    {
        $this->initialize();
        $this->buildHeader('partial:_header.html.tpl', 'Dashboard', $this->_organization->name, '');
        $this->template->organization = $this->_organization;
    }

    public function start ()
    {
        $this->initialize();
        $this->forward('syllabus/start', [
            'organization' => $this->_organization, 
            'routeBase' => $this->_routeBase
        ]);
    }

    public function edit ()
    {
        $this->initialize();
        $this->template->addBreadcrumb($this->_routeBase."syllabus/$this->_syllabusId", 'Edit Template');

        $this->forward('syllabus/new', [
            'organization' => $this->_organization, 
            'routeBase' => $this->_routeBase,
            'id' => $this->_syllabusId
        ]);
    }

    public function view ()
    {
        $this->initialize();
        $this->forward("syllabus/$this->_syllabusId/view", [
            'id' => $this->_syllabusId,
            'organization' => $this->_organization, 
            'routeBase' => $this->_routeBase,
            'templateAuthorizationId' => $this->_organization->templateAuthorizationId
        ]);
    }

    public function delete ()
    {
        $this->initialize();
        $this->forward("syllabus/$this->_syllabusId/delete", [
            'id' => $this->_syllabusId,
            'organization' => $this->_organization, 
            'routeBase' => $this->_routeBase,
            'templateAuthorizationId' => $this->_organization->templateAuthorizationId
        ]);
    }

    public function startWith ()
    {
        $this->initialize();
        if ($this->_organization && $this->_organization->userHasRole($this->requireLogin(), 'creator'))
        {
            $this->forward("syllabus/startwith/$this->_syllabusId", [
                'id' => $this->_syllabusId,
                'organization' => $this->_organization, 
                'routeBase' => $this->_routeBase,
                'templateAuthorizationId' => $this->_organization->templateAuthorizationId
            ]);         
        }
    }

    public function listTemplates ()
    {
        $this->initialize();
        $this->buildHeader('partial:_header.html.tpl', 'Templates', $this->_organization->name, '');
        $this->addBreadcrumb($this->_routeBase.'templates', 'View Templates');
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');

        $templates = $syllabi->find(
            $syllabi->templateAuthorizationId->equals($this->_organization->templateAuthorizationId),
            ['orderBy' => '-createdDate']
        );
        
        $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
        foreach ($templates as $template)
        {
            $tid = $template->id;
            $results = $this->getScreenshotUrl($tid, $screenshotter);
            $template->imageUrl = $results->imageUrls->$tid;
        }

        $this->template->templates = $templates;
        $this->template->organization = $this->_organization;
    }

    public function myOrganizations ()
    {
        $this->initialize();
        // $this->buildHeader('partial:_header.edit.html.tpl', 'My Organizations', '', '');
        $viewer = $this->requireLogin();
        $departmentSchema = $this->schema('Syllabus_AcademicOrganizations_Department');
        $collegeSchema = $this->schema('Syllabus_AcademicOrganizations_College');
        // $groupSchema = $this->schema('Syllabus_Organizations_Group');
        $organizations = [];

        if ($this->hasPermission('admin'))
        {
            $organizations['colleges'] = $collegeSchema->getAll(['orderBy'=>'name']) ?? [];
            $organizations['departments'] = $departmentSchema->getAll(['orderBy'=>'name']) ?? [];
        }
        elseif ($viewer->classDataUser)
        {
            $authZ = $this->getAuthorizationManager();
            $azids = $authZ->getObjectsForWhich($viewer, 'view org templates');
            $organizations['colleges'] = $collegeSchema->getByAzids($azids, null, ['orderBy'=>'name']) ?? [];
            $organizations['departments'] = $departmentSchema->getByAzids($azids, null, ['orderBy'=>'name']) ?? [];
        }

        $this->template->allOrganizations = $organizations;
    }

    public function listOrganizations ()
    {
        $this->initialize();
        // $this->buildHeader('partial:_header.edit.html.tpl', $this->organization->organizationType.'s', '', '');
        $viewer = $this->requireLogin();
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('organizations', 'My Organizations');
        $organizations = [];

        if ($this->hasPermission('admin'))
        {
            $organizations = $this->organizationSchema->getAll(['orderBy'=>'name']) ?? [];
        }
        else
        {
            $authZ = $this->getAuthorizationManager();
            $azids = $authZ->getObjectsForWhich($viewer, 'view org templates');
            $organizations = $this->organizationSchema->getByAzids($azids, null, ['orderBy'=>'name']) ?? [];
        }

        $this->template->organizations = $organizations;    
    }

    public function manageOrganization ()
    {
        $this->initialize();
        $viewer = $this->requireLogin();
        $this->_organization->requireRole('manager', $this);
    }

    public function manageSubmissions ()
    {
        $this->initialize();
        $viewer = $this->requireLogin();
        if (!$this->_organization->userHasRole($viewer, 'moderator'))
        {
            $this->_organization->requireRole('manager', $this);
        }
        $this->addBreadcrumb($this->_routeBase . 'submissions', 'Manage Submissions');

        $submissions = $this->schema('Syllabus_Syllabus_Submission');
        $campaigns = $this->schema('Syllabus_Syllabus_SubmissionCampaign');
        $semesters = $this->schema('Syllabus_Admin_Semester');

        $activeSemester = $semesters->findOne(
            $semesters->startDate->before(new DateTime)->andIf(
                $semesters->endDate->after(new DateTime)
            )
        );
        $cond = $campaigns->allTrue(
            $campaigns->organizationAuthorizationId->equals($this->_organization->templateAuthorizationId),
            $campaigns->semester_id->isNotNull(),
            $campaigns->semester_id->equals($activeSemester->id)
        );
        $activeCampaign = $campaigns->findOne($cond);
        $allCampaigns = $campaigns->find(
            $campaigns->organizationAuthorizationId->equals($this->_organization->templateAuthorizationId),
            ['orderBy' => ['-semester_id', '-dueDate']]
        );

        $this->template->updatedSubmission = $this->request->getQueryParameter('s');
        $this->template->allCampaigns = $allCampaigns;
        $this->template->activeCampaign = $activeCampaign;
        $this->template->routeBase = $this->_routeBase;
    }

    public function editSubmission ()
    {
        $this->initialize();
        $viewer = $this->requireLogin();
        if (!$this->_organization->userHasRole($viewer, 'moderator'))
        {
            $this->_organization->requireRole('manager', $this);
        }
        $submission = $this->requireExists(
            $this->schema('Syllabus_Syllabus_Submission')->get($this->getRouteVariable('sid'))
        );
        $this->addBreadcrumb($this->_routeBase . '/submissions/' . $submission->id, 'Edit Submission');

        $submissions = $this->schema('Syllabus_Syllabus_Submission');
        
        if ($this->request->wasPostedByUser())
        {
            $submission->modifiedDate = new DateTime;
            switch ($this->getPostCommand())
            {
                case 'disable':
                    $submission->status = 'disabled';
                    $submission->modifiedDate = new DateTime;
                    $submission->log .= "
                    <li>
                        Submission disabled for course #{$submission->course_section_id} on 
                        {$submission->modifiedDate->format('F jS, Y - h:i a')}.
                    </li>";
                    $submission->campaign->log .= "
                    <li>
                        Submission #{$submission->id} disabled on 
                        {$submission->modifiedDate->format('F jS, Y - h:i a')}.
                    </li>";
                    break;

                case 'enable':
                    $submission->status = 'open';
                    $submission->modifiedDate = new DateTime;
                    $submission->log .= "
                    <li>
                        Submission re-enabled for course #{$submission->course_section_id} on 
                        {$submission->modifiedDate->format('F jS, Y - h:i a')}.
                    </li>";
                    $submission->campaign->log .= "
                    <li>
                        Submission #{$submission->id} re-enabled and set to 'open' on 
                        {$submission->modifiedDate->format('F jS, Y - h:i a')}.
                    </li>";
                    break;

                case 'approve':
                    $submission->status = 'approved';
                    $submission->approvedDate = new DateTime;
                    $submission->feedback = $this->request->getPostParameter('feedback');
                    $submission->log .= "
                    <li>
                        Submission approved on {$submission->modifiedDate->format('F jS, Y - h:i a')}.
                    </li>";
                    $submission->campaign->log .= "
                    <li>
                        Submission #{$submission->id} set to 'approved' on 
                        {$submission->modifiedDate->format('F jS, Y - h:i a')}.
                    </li>";
                    break;

                case 'deny':
                    $submission->status = 'denied';
                    $submission->feedback = $this->request->getPostParameter('feedback');
                    $submission->log .= "
                    <li>
                        Submission denied on {$submission->modifiedDate->format('F jS, Y - h:i a')}.
                    </li>";
                    $submission->campaign->log .= "
                    <li>
                        Submission #{$submission->id} set to 'denied' on 
                        {$submission->modifiedDate->format('F jS, Y - h:i a')}.
                    </li>";
                    break;
            }
            $submission->save();
            $submission->campaign->save();

            $this->flash('Submission status updated');
            $this->response->redirect($this->_routeBase . 'submissions?s=' . $submission->id);
        }
    }

    public function editCampaign ()
    {
        $this->initialize();
        $viewer = $this->requireLogin();
        $campaigns = $this->schema('Syllabus_Syllabus_SubmissionCampaign');
        $cid = $this->getRouteVariable('cid');
        $campaign = $cid === 'new' ? $campaigns->createInstance() : $campaigns->get($cid);
        if (!$this->_organization->userHasRole($viewer, 'moderator'))
        {
            $this->_organization->requireRole('manager', $this);
        }

        $this->addBreadcrumb($this->_routeBase . 'submissions', 'Submissions');
        if (isset($campaign->id))
        {
            $this->addBreadcrumb($this->_routeBase . 'campaigns/' . $campaign->id, 'Manage Campaign');
        }
        else
        {
            $this->addBreadcrumb($this->_routeBase . 'campaigns/new', 'New Campaign');
        }

        $campaignSemesters = $campaigns->findValues('semester_id', 
            $campaigns->organizationAuthorizationId->equals($this->_organization->templateAuthorizationId)
        );

        $semesters = $this->schema('Syllabus_Admin_Semester');
        $activeSemester = $semesters->findOne(
            $semesters->startDate->before(new DateTime)->andIf(
                $semesters->endDate->after(new DateTime)
            )
        );

        if ($this->request->wasPostedByUser())
        {
            $data = $this->request->getPostParameters();
            $semesterUsed = false;
            if (($cid !== 'new' && $data['semester'] !== $campaign->semester->id) ||
                ($cid === 'new' && in_array($data['semester'], $campaignSemesters)))
            {
                $semesterUsed = true;
            }
            switch ($this->getPostCommand())
            {
                case 'save':

                    if (isset($data['semester']) && !$semesterUsed)
                    {
                        if (!($semester = $semesters->get($data['semester'])))
                        {
                            $this->flash('Invalid semester selected.');
                            $this->response->redirect($this->_routeBase . 'campaigns/' . $cid);
                        }

                        try
                        {
                            $campaign->dueDate = new DateTime($data['dueDate']);
                        }
                        catch (Exception $e)
                        {
                            $this->flash('Invalid due date entered.');
                            $this->response->redirect($this->_routeBase . 'campaigns/' . $cid);
                        }

                        $campaign->semester_id = $semester->id;
                        $campaign->organizationAuthorizationId = $this->_organization->templateAuthorizationId;
                        $campaign->description = $data['description'] ?? '';
                        $campaign->required = $data['required'] === '1';
                        $campaign->modifiedDate = new DateTime;
                        $intro = ($cid === 'new' ? 'New' : 'Edited');
                        $log = $campaign->log !== null && $campaign->log !== '' ? $campaign->log : '';
                        $log .= "
                        <li>
                            {$intro} campaign on {$campaign->modifiedDate->format('F jS, Y - h:i a')} 
                            with a due date of {$campaign->dueDate->format('F jS, Y - h:i a')}.
                        </li>";
                        $campaign->log = $log;
                        $campaign->save();

                        if ($cid === 'new')
                        {
                            $submissions = $this->schema('Syllabus_Syllabus_Submission');
                            $courseSections = $this->schema('Syllabus_ClassData_CourseSection');
                            $organizationCourses = $courseSections->find(
                                $courseSections->department_id->equals($this->_organization->id),
                                ['orderBy' => 'id']
                            );
                            $counter = 0;
                            foreach ($organizationCourses as $course)
                            {
                                if ($course->getTerm(true) === $campaign->semester->internal)
                                {
                                    $submission = $submissions->createInstance();
                                    $submission->campaign_id = $campaign->id;
                                    $submission->course_section_id = $course->id;
                                    $submission->status = 'open';
                                    $submission->modifiedDate = new DateTime;
                                    $submission->log = "<li>Submission status opened on {$campaign->modifiedDate->format('F jS, Y - h:i a')}   [campaign id #{$campaign->id}].</li>";
                                    $submission->save();
                                    $counter++;
                                }
                            }
                            $campaign->log .= "
                            <li>
                                Submissions are now open for {$counter} course sections for {$campaign->semester->display}.
                            </li>";
                            $campaign->save();
                        }
                        $this->flash(
                            "The submissions campaign for {$this->_organization->name} for {$campaign->semester->display} has been saved."
                        );
                    }
                    else
                    {
                        $this->flash('There already exists a campaign for the semester you selected.', 'danger');
                    }

                    $this->response->redirect($this->_routeBase . 'submissions');
                    break;

                case 'delete':
                    foreach ($campaign->submissions as $submission)
                    {
                        $submission->deleted = true;
                        $submission->log .= "<li>Campaign #{$campaign->id} deleted.</li>";
                        $submission->save();
                    }
                    $campaign->delete();
                    $this->flash(
                        'Campaign deleted. All submissions have been removed from the courses in this department.',
                        'success'
                    );
                    $this->response->redirect($this->_routeBase . 'submissions');
                    break;
            }
        }

        $this->template->campaignSemesters = $campaignSemesters;
        $this->template->semesters = $semesters->getAll(['orderBy' => '-startDate']);
        $this->template->activeSemester = $activeSemester;
        $this->template->campaign = $campaign;
        $this->template->organization = $this->_organization;
    }

    public function manageUsers ()
    {
        $this->initialize();
        $viewer = $this->requireLogin();
        $organization = $this->getOrganization($this->getRouteVariable('oid'));
        $organization->requireRole('manager', $this);
        $orgType = $organization->routeName;
        $routeBase = $orgType . '/' .$organization->id . '/';
        $this->buildHeader('partial:_header.html.tpl', 'Manage Users', $organization->name, '');
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('organizations', 'My Organizations');
        $this->addBreadcrumb($organization->routeName, ucfirst($organization->routeName));
        $this->addBreadcrumb($routeBase, $organization->abbreviation . ' Home');
        $this->addBreadcrumb($routeBase, 'Manage Users');

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
  
        $totalAccounts = $organization->getRoleUserCount('member', true, $condition);
        $pageCount = ceil($totalAccounts / $limit);
        $this->template->pagesAroundCurrent = $this->getPagesAroundCurrent($page, $pageCount, $organization);

        // DEBUG override of optionMap
        $memberList = $organization->getRoleUsers('member', true, $condition, $optionMap);
        

        $this->template->searchQuery = $searchQuery;
        $this->template->totalAccounts = $totalAccounts;
        $this->template->pageCount = $pageCount;
        $this->template->currentPage = $page;
        $this->template->accountList = $memberList;
        $this->template->sortBy = $sortBy;
        $this->template->dir = $sortDir;
        $this->template->oppositeDir = ($sortDir == 'asc' ? 'desc' : 'asc');
        $this->template->limit = $limit;

        $this->template->organization = $organization;
    }

    public function editUser ()
    {
        $this->initialize();
        $viewer = $this->requireLogin();
        $organization = $this->getOrganization($this->getRouteVariable('oid'));
        $organization->requireRole('manager', $this);
        $user = $this->requireExists($this->schema('Bss_AuthN_Account')->get($this->getRouteVariable('uid')));
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('organizations', 'My Organizations');
        $this->addBreadcrumb($organization->routeName, ucfirst($organization->routeName));
        $this->addBreadcrumb($organization->routeName.'/'.$organization->id, $organization->name);
        $this->addBreadcrumb($organization->routeName.'/'.$organization->id.'/users', 'Users');
        $this->addBreadcrumb($organization->routeName.'/'.$organization->id.'/users', $user->fullName);

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






