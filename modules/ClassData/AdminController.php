<?php

/**
 */
class Syllabus_ClassData_AdminController extends At_Admin_Controller
{
    public static function getRouteMap ()
    {
        return [
            'admin/classdata'               => ['callback' => 'index'],
            'admin/classdata/semesters'     => ['callback' => 'setSemesters'],
            'admin/classdata/import'        => ['callback' => 'import'],
            // 'admin/departments'             => ['callback' => 'departments'],
            // 'admin/departments/:id'         => ['callback' => 'editDepartment'],
            // 'admin/departments/:id/courses' => ['callback' => 'departmentCourses'],
            // 'admin/contacts'                => ['callback' => 'contacts']
        ];
    }

    public function beforeCallback ($callback)
    {
        parent::beforeCallback($callback);
        $this->requirePermission('admin');
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('admin', 'Admin');
    }    
    
    public function index ()
    {
        $siteSettings = $this->getApplication()->siteSettings;
        
        if ($this->getPostCommand() == 'save' && $this->request->wasPostedByUser())
        {
            $siteSettings->setProperty('classdata-api-url',$this->request->getPostParameter('classdata-api-url'));
            $siteSettings->setProperty('classdata-api-key',$this->request->getPostParameter('classdata-api-key'));
            $siteSettings->setProperty('classdata-api-secret',$this->request->getPostParameter('classdata-api-secret'));
        }
        
        $this->template->classdataApiUrl = $siteSettings->getProperty('classdata-api-url');
        $this->template->classdataApiKey = $siteSettings->getProperty('classdata-api-key');
        $this->template->classdataApiSecret = $siteSettings->getProperty('classdata-api-secret');
    }

    public function defaults ()
    {
        $siteSettings = $this->getApplication()->siteSettings;
        
        if ($this->getPostCommand() == 'save' && $this->request->wasPostedByUser())
        {           
            if ($email = $this->request->getPostParameter('cs-default-email'))
            {
                $siteSettings->setProperty('cs-default-email', $email);
            }
        }
        
        $this->template->email = $siteSettings->getProperty('cs-default-email');
    }


    public function import ()
    {
        if ($this->request->wasPostedByUser())
        {
            $service = new Syllabus_ClassData_Service($this->application);
            $service->importOrganizations();

            $semesterCodes = $this->application->siteSettings->semesters ?? '2195';
            if (!is_array($semesterCodes))
            {
                $semesterCodes = explode(',', $semesterCodes);
            }
            foreach ($semesterCodes as $semesterCode)
            {
                $service->import($semesterCode);
            }
        }
    }

    /**
     */
    public function setSemesters ()
    {
        $this->setPageTitle('Set active and Vis semesters');
        
        if ($this->request->wasPostedByUser())
        {
            $this->application->siteSettings->semesters = $this->request->getPostParameter('semesters');
        }
        
        
        $this->template->semesters = $this->application->siteSettings->semesters;
    }


    public function departments ()
    {
        $this->setPageTitle('Manage departments');

        $this->template->departments = $this->schema('Syllabus_AcademicOrganizations_Department')->getAll(['orderBy' => 'name']);
    }


    public function editDepartment ()
    {
        $this->addBreadcrumb('admin/departments', 'Manage Departments');
        $department = $this->helper('activeRecord')->fromRoute('Syllabus_AcademicOrganizations_Department', 'id', ['allowNew' => true]);

        $this->setPageTitle(($department->inDatasource ? 'Edit' : 'New') . ' Department');

        $contacts = $this->schema('At_CS_Contact');
        $allContacts = $contacts->getAll(['orderBy' => ['+lastName', '+firstName']]);

        $processInfo = ['skipIfEmpty' => true];

        if ($this->request->wasPostedByUser())
        {
            switch ($this->getPostCommand()) {
                case 'remove-contact':
                    $data = $this->getPostCommandData();
                    $contactId = array_shift(array_keys($data));
                    if ($contact = $contacts->get($contactId))
                    {
                        $department->contacts->remove($contact);
                        $this->userMessage('Contact Removed', 'Removed ' . $contact->firstName . ' ' . $contact->lastName);
                    }
                
                case 'save':
                    $params = ['name'];

                    if (!$department->inDatasource)
                    {
                        $params[] = 'id';
                    }

                    if ($this->processSubmission($department, $params, $processInfo))
                    {
                        if ($contactId = $this->request->getPostParameter('contact_id'))
                        {
                            if ($contact = $contacts->get($contactId))
                            {
                                $department->contacts->add($contact);
                            }
                        }

                        $department->save();
                        $this->template->success = 'Department Saved';
                    }

                    if (!empty($processInfo['errorMap'])) 
                    {
                        $this->template->errorMap = $processInfo['errorMap'];
                    }
            }
        }

        $this->template->department = $department;
        $this->template->contacts = $allContacts;
    }


    public function departmentCourses ()
    {
        $department = $this->helper('activeRecord')->fromRoute('Syllabus_AcademicOrganizations_Department', 'id');
        $this->setPageTitle('Department Courses');
        $this->addBreadcrumb('admin/departments', 'Manage Departments');

        $courses = $this->schema('Syllabus_ClassData_CourseSection');
        $this->template->department = $department;
        $this->template->courses = $courses->find($courses->department_id->equals($department->id), ['orderBy' => 'shortName']);
    }


    public function contacts ()
    {
        $this->setPageTitle('Manage department contacts');

        $this->template->contacts = $this->schema('At_CS_Contact')->getAll(['orderBy' => ['+lastName', '+firstName']]);
    }


    public function editJobCode ()
    {
        $jobCode = $this->helper('activeRecord')->fromRoute('At_CS_JobCode', 'id', ['allowNew' => true]);
        $this->addBreadcrumb('admin/faculty', 'Manage Faculty');
        $this->setPageTitle(($jobCode->inDatasource ? 'Edit' : 'New') . ' Job Code');
        $processInfo = ['skipIfEmpty' => true];

        if ($this->request->wasPostedByUser())
        {
            if ($this->processSubmission($jobCode, ['id', 'description'], $processInfo))
            {
                $jobCode->tenureTrack = $this->request->getPostParameter('tenureTrack', 'f');
                $jobCode->save();
                $this->response->redirect('/admin/faculty');
            }

            if (!empty($processInfo['errorMap'])) 
            {
                $this->template->errorMap = $processInfo['errorMap'];
            }
        }

        $this->template->jobCode = $jobCode;
    }



    public function audit ()
    {
        $this->setPageTitle('Review audit log');
        $audits = $this->schema('At_CS_Audit');

        $conds = [];
        $disableJoins = [];

        if (($startDate = $this->parseDate('start', '00:00:00')))
        {
            $conds[] = $audits->dt->afterOrEquals($startDate);
        }

        if (($endDate = $this->parseDate('end', '23:59:59', 'now')))
        {
            $conds[] = $audits->dt->beforeOrEquals($endDate);
        }

        if (($userId = $this->request->getQueryParameter('userId')))
        {
            $userId = trim(strtolower($userId));
            $matches = null;
            $fnCond = null;

            if (preg_match('/^([a-z]+)(\s+|,\s+)([a-z]+)$/i', $userId, $matches))
            {
                $first = $matches[1];
                $last = $matches[3];

                if ($matches[2][0] == ',')
                {
                    list($last, $first) = [$first, $last];
                }

                $fnCond = $audits->allTrue(
                    $audits->user->firstName->lower()->like("%$first%"),
                    $audits->user->lastName->lower()->like("%$last%")
                );
            }

            $conds[] = $audits->anyTrue(array_filter([
                $audits->user_id->like("%$userId%"),
                $audits->user->firstName->lower()->like("%$userId%"),
                $audits->user->lastName->lower()->like("%$userId%"),
                $audits->user->emailAddress->lower()->like("%$userId%"),
                $fnCond,
            ]));
        }
        else
        {
            $disableJoins[] = 'user';
        }

        if (($courseId = $this->request->getQueryParameter('courseId')))
        {
            $courseId = trim(strtoupper($courseId));
            $massagedCourseId = $courseId;
            $matches = null;

            if (preg_match('/^([a-z]+)[ -]?([0-9]+)(?:[. -]([0-9]+))?(.*)/i', $courseId, $matches))
            {
                $massagedCourseId = $matches[1] . '-' . str_pad($matches[2], 4, '0', STR_PAD_LEFT) . '-';

                if (!empty($matches[3]))
                {
                    $massagedCourseId .= str_pad($matches[3], 2, '0', STR_PAD_LEFT);
                }

                $massagedCourseId .= $matches[4];
            }

            $conds[] = $audits->anyTrue(
                $audits->course_id->equals(intval($courseId)),
                $audits->course->shortName->upper()->like("%$courseId%"),
                $audits->course->shortName->upper()->like("%$massagedCourseId%")
            );
        }
        else
        {
            $disableJoins[] = 'course';
        }

        if (($type = $this->request->getQueryParameter('type')))
        {
            switch ($type)
            {
                case 'enrollments':
                case 'student':
                case 'instructor':
                    $conds[] = $audits->user_id->isNotNull();
                    $conds[] = $audits->course_id->isNotNull();
                    if ($type != 'enrollments')
                    {
                        $conds[] = $audits->extraData->equals($type);
                    }
                    break;
                case 'course':
                    $conds[] = $audits->user_id->isNull();
                    $conds[] = $audits->course_id->isNotNull();
                    break;
                case 'user':
                    $conds[] = $audits->user_id->isNotNull();
                    $conds[] = $audits->course_id->isNull();
                    break;
            }
        }

        if (($crumb = $this->request->getQueryParameter('crumb')))
        {
            $crumbHref = $this->request->getQueryParameter('crumb_href');
            $this->addBreadcrumb($crumbHref, $crumb);
        }

        $this->template->startDateObj = $startDate;
        $this->template->startDate = $this->request->getQueryParameter('start');
        $this->template->endDateObj = $endDate;
        $this->template->endDate = $this->request->getQueryParameter('end', 'now');
        $this->template->userId = $userId;
        $this->template->courseId = $courseId;
        $this->template->type = $type;

        $cond = ($conds ? $audits->allTrue($conds) : null);
        $count = $audits->count($cond, ['disableJoins' => $disableJoins, 'distinct' => false]);
        $perPage = 200;
        $page = max(1, min(ceil($count / $perPage), $this->request->getQueryParameter('page')));

        $qps = $this->request->getQueryParameters();
        $paginationHelper = $this->helper('pagination');
        $pagination = $paginationHelper->build('admin/audit', $qps, $page, $perPage, $count);

        if (isset($qps['p']))
        {
            unset($qps['p']);
        }

        $this->template->hasFilters = !!$qps;

        $rows = $audits->find($cond, [
            'disableJoins' => $disableJoins,
            'orderBy' => ['-dt', '+course_id', '+user_id', '-id'],
            'limit' => $perPage,
            'offset' => $pagination['itemRange'][0],
        ]);

        $courseMap = [];
        $userMap = [];

        foreach ($rows as $row)
        {
            if ($row->course_id)
            {
                $courseMap[$row->course_id] = true;
            }

            if ($row->user_id)
            {
                $userMap[$row->user_id] = true;
            }
        }

        if ($courseMap)
        {
            $courses = $this->schema('Syllabus_ClassData_CourseSection');
            foreach ($courses->find($courses->id->inList(array_keys($courseMap)), ['returnIterator' => true]) as $course)
            {
                $courseMap[$course->id] = $course->shortName;
            }
        }

        if ($userMap)
        {
            $users = $this->schema('Syllabus_ClassData_User');
            foreach ($users->find($users->id->inList(array_keys($userMap)), ['returnIterator' => true]) as $user)
            {
                $userMap[$user->id] = implode(', ', array_filter([$user->lastName, $user->firstName]));
            }
        }

        $this->template->audits = $rows;
        $this->template->courseMap = $courseMap;
        $this->template->userMap = $userMap;
        $this->template->page = $page;
        $this->template->pagination = $pagination;
    }


    public function sectionInfo ()
    {
        $this->addBreadcrumb('admin/audit', 'Audit log');
        $courses = $this->schema('Syllabus_ClassData_CourseSection');
        $course = $this->requireExists($courses->get($this->getRouteVariable('id')));

        if ($this->request->wasPostedByUser())
        {
            if ($departmentId = $this->request->getPostParameter('department_id'))
            {
                $department = $this->schema('Syllabus_AcademicOrganizations_Department')->get($departmentId);

                if ($department)
                {
                    $course->department = $department;
                }
            }

            $course->save();
            $this->template->success = 'The section was saved.';
        }

        $this->setPageTitle("$course->shortName ({$course->id})");
        $this->template->course = $course;
        $this->template->academicGroups = $this->schema('Syllabus_AcademicOrganizations_College')->getAll(['orderBy' => 'name']);
    }


    public function userInfo ()
    {
        $this->addBreadcrumb('admin/audit', 'Audit log');
        $users = $this->schema('Syllabus_ClassData_User');
        $user = $this->requireExists($users->get($this->getRouteVariable('id')));

        if ($this->request->wasPostedByUser())
        {
            switch ($this->getPostCommand()) {
                case 'save':
                    $user->organizations->save();
                    break;
                
                case 'add-department':
                    if ($departmentId = $this->request->getPostParameter('department'))
                    {
                        $department = $this->schema('Syllabus_AcademicOrganizations_Department')->get($departmentId);

                        if ($department)
                        {
                            $user->organizations->add($department);
                            $user->organizations->save();
                            $user->save();
                        }
                    }
                    break;
            }
            
        }

        $this->setPageTitle("$user->lastName, $user->firstName ({$user->id})");
        $this->template->user = $user;
        $this->template->academicGroups = $this->schema('Syllabus_AcademicOrganizations_College')->getAll(['orderBy' => 'name']);
    }


    public function courseInfo ()
    {
        $archives = $this->schema('At_CS_ArchiveCourse');
        $course = $this->requireExists($archives->getByCatalog_number($this->getRouteVariable('id')));

        $this->setPageTitle("Course Catalog #{$course->catalog_number}");
        $this->template->course = $course;
    }


    protected function parseDate ($name, $defaultTime, $defaultValue = null)
    {
        if (($dt = $this->request->getQueryParameter($name, $defaultValue)))
        {
            // If it doesn't appear to have a time on it, tack one on.
            if (!preg_match('/[0-9]{1,2}:[0-9]{1,2}/', $dt))
            {
                $dt .= " $defaultTime";
            }

            try
            {
                $dt = new DateTime($dt);
                return $dt;
            }
            catch (Exception $ex)
            {
                $this->template->{"{$name}_error"} = 'Invalid date/time format.';
            }
        }

        return null;
    }


    protected function parsePostDate ($name, $defaultTime, $defaultValue = null)
    {
        if (($dt = $this->request->getPostParameter($name, $defaultValue)))
        {
            // If it doesn't appear to have a time on it, tack one on.
            if (!preg_match('/[0-9]{1,2}:[0-9]{1,2}/', $dt))
            {
                $dt .= " $defaultTime";
            }

            try
            {
                $dt = new DateTime($dt);
                return $dt;
            }
            catch (Exception $ex)
            {
                $this->template->{"{$name}_error"} = 'Invalid date/time format.';
            }
        }

        return null;
    }
}
