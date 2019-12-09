<?php

/**
 */
class Syllabus_Admin_Controller extends Syllabus_Master_Controller
{
    public static function getRouteMap ()
    {
        return [       
            // '/admin' => ['callback' => 'index'],
            // '/admin/colophon' => ['callback' => 'colophon'],
            '/admin/settings/email' => array('callback' => 'emailSettings'),
			'/admin/apc' => ['callback' => 'clearMemoryCache'],
            '/admin/cron' => ['callback' => 'cron'],
            '/admin/settings/siteNotice' => ['callback' => 'siteNotice'],
            '/admin/semesters' => ['callback' => 'editSemesters'],
            '/admin/files/:fid/download' => array( 'callback' => 'download', 'fid' => '[0-9]+'),
        ];
    }
    
    protected function beforeCallback ($callback)
    {
        parent::beforeCallback($callback);
        $this->requirePermission('admin');
        $this->template->clearBreadcrumbs();
        $this->addBreadcrumb('home', 'Home');
        $this->addBreadcrumb('admin', 'Admin');
    }

    /**
     * Dashboard.
     */
    public function index ()
    {
        $this->setPageTitle('Administrate');
    }

    public function editSemesters ()
    {
        $this->setPageTitle('Configure Semesters');
        $semesters = $this->schema('Syllabus_Admin_Semester');
        $errors = array();
        $message = '';

        if ($this->request->wasPostedByUser())
        {
            if ($command = $this->getPostCommand())
            {
                switch ($command)
                {
                    case 'remove':
                        if ($semester = $semesters->get(key($this->getPostCommandData())))
                        {
                            $selected = $semester->display;
                            $semester->delete();
                            $message = "The $selected semester has been deleted";
                            $this->flash($message, 'success');
                        }
                        break;

                    case 'add':
                        $semester = $semesters->createInstance();
                        
                        if ($startDate = $this->request->getPostParameter('startDate'))
                        {
                            $semester->startDate = new DateTime($startDate);
                        }
                        if ($endDate = $this->request->getPostParameter('endDate'))
                        {
                            $semester->endDate = new DateTime($endDate);
                        }
             
                        $term = $this->request->getPostParameter('term');
                        $semester->display = $term . ' ' . $semester->startDate->format('Y');
                        $codes = ['Spring'=>3, 'Summer'=>5, 'Fall'=>7, 'Winter'=>1];
                        $m = $semester->startDate->format('m');
                        $y = $semester->startDate->format('Y');
                        $y = $y[0] . substr($y, 2);
                        if ($term === 'Winter' && $m = '12') { $y++; }
                        $semester->internal = $y . $codes[$term];
                        $semester->active = $this->request->getPostParameter('active', true);

                        $errors = $semester->validate();
                        
                        if (empty($errors))
                        {
                            $semester->save();
                            $message = 'Semester created';
                        }
                        break;

                    case 'activate':
                        $semesterData = $this->request->getPostParameter('semesters', []);
                        $activeData = $semesterData['active'] ?? [];
                        unset($semesterData['active']);

                        if (isset($semesterData))
                        {
                            foreach ($semesterData as $id)
                            {
                                $semester = $semesters->get($id);
                                if (isset($activeData) && isset($activeData[$id]))
                                {
                                    $semester->active = true;
                                }
                                else
                                {
                                    $semester->active = false;
                                }
                                $semester->save();
                            }
                        }

                        $errors = $semester->validate() ?? [];
                        break; 
                }
            }
        }

        $view = $this->request->getQueryParameter('view', 'recent');
        if ($view !== 'all')
        {
            if ($this->hasPermission('admin'))
            {
                $semesters = $semesters->find($semesters->startDate->after(new DateTime('-3 years')), ['orderBy' => '-startDate']);
            }
        }
        else
        {
            $semesters = $semesters->getAll(['orderBy' => '-startDate']);
        }
        
        
        $this->template->semesters = $semesters;
        $this->template->terms = Syllabus_Admin_Semester::GetTerms();
        $this->template->view = $view;
        $this->template->message = $message;
        $this->template->errors = $errors;
    }

    /**
     * Set the site notice.
     */
    public function siteNotice ()
    {
        $this->addBreadcrumb('admin', 'Administrate');
        $this->setPageTitle('Site notice');
        $settings = $this->getApplication()->siteSettings;
        
        if ($this->request->wasPostedByUser())
        {
            $sanitizer = new Bss_RichText_HtmlSanitizer;
            $settings->siteNotice = $sanitizer->sanitize($this->request->getPostParameter('siteNotice'));
            $this->response->redirect('admin');
        }
        
        $this->template->siteNotice = $settings->siteNotice;
    }

	/**
	 */
	public function clearMemoryCache ()
	{
		if (function_exists('apc_clear_cache'))
		{
			$this->template->cacheExists = true;
			
			if ($this->request->wasPostedByUser())
			{
                set_time_limit(0);
                $this->request->getSession()->release();
                
				$this->userMessage('Cleared op-code and user cache.');
				apc_clear_cache();
				apc_clear_cache('user');
                
                // Force the permission cache to rebuild.
                $this->getAuthorizationManager()->updateCache();
			}
		}
	}
    
    public function cron ()
    {
        $moduleManager = $this->application->moduleManager;
        $xp = $moduleManager->getExtensionPoint('bss:core:cron/jobs');
        $lastRunDates = $xp->getLastRunDates();
        $cronJobMap = array();
        
        if ($this->request->wasPostedByUser() && $this->getPostCommand() === 'invoke')
        {
            $data = $this->getPostCommandData();
            $now = new DateTime;
            
            foreach ($data as $name => $nonce)
            {
                if (($job = $xp->getExtensionByName($name)))
                {
                    $xp->runJob($name);
                    $lastRunDates[$name] = $now;
                }
            }
        }
        
        foreach ($xp->getExtensionDefinitions() as $jobName => $jobInfo)
        {
            $cronJobMap[$jobName] = array(
                'name' => $jobName,
                'instanceOf' => $jobInfo[0],
                'module' => $jobInfo[1],
                'lastRun' => (isset($lastRunDates[$jobName]) ? $lastRunDates[$jobName]->format('c') : 'never'),
            );
        }
        
        $this->template->cronJobs = $cronJobMap;
    }


    public function download ()
    {
        $account = $this->requireLogin();
        
        $fid = $this->getRouteVariable('fid');
        $file = $this->requireExists($this->schema('Syllabus_Files_File')->get($fid));
        
        if ($file->uploadedBy && ($account->id != $file->uploadedBy->id))
        {
            
            if ($item = $this->getRouteVariable('item'))
            {
                $authZ = $this->getAuthorizationManager();
                $extension = $item->extension;
                
                if ($authZ->hasPermission($account, $extension->getItemViewTask(), $item))
                {
                    $file->sendFile($this->response);
                }
            }
            
            // $this->requirePermission('file download');
        }
        
        $file->sendFile($this->response);
    }

    public function reports ()
    {
        set_time_limit(0);
        $viewer = $this->requireLogin();
        $this->requirePermission('reports generate');
        $migrationDate = new DateTime('2018-05-01');

        $courseSchema = $this->schema('Syllabus_Courses_Course');
        $obsSchema = $this->schema('Syllabus_Rooms_Observation');
        $resSchema = $this->schema('Syllabus_Rooms_Reservation');
        $roomSchema = $this->schema('Syllabus_Rooms_Room');
        $semSchema = $this->schema('Syllabus_Semesters_Semester');
        $userSchema = $this->schema('Bss_AuthN_Account');
        $roleSchema = $this->schema('Syllabus_AuthN_Role');

        $tomorrow = new DateTime('+1 day');
        $filename = 'CC-Observation-Report-' . date('Y-m-d') . '.csv';
        $obsData = array();
        $orgs = array();

        if ($this->request->wasPostedByUser())
        {
            $from = $this->request->getPostParameter('from', 0);
            $until = $this->request->getPostParameter('until', $tomorrow);

            try {
                $test = new DateTime($from);
                $test = new DateTime($until);
            } catch (Exception $e) {
                $this->flash('Invalid Date/Time format. Please try again.');
                $this->response->redirect('admin/reports/generate');
                exit;
            }

            $observations = $obsSchema->find(
                $obsSchema->startTime->afterOrEquals($from)->andIf(
                $obsSchema->startTime->beforeOrEquals($until)),
                array('orderBy' => 'startTime')
            );

            // NOTE: college & department fields will only be fetched post-migration
            foreach ($observations as $obs)
            {
                if ($obs->duration)
                {
                    $course = $obs->purpose->object->course;
                    if (!in_array($course->shortName, array_keys($orgs)))
                    {   // cache API results
                        $orgs[$course->shortName] = array();
                        $orgs[$course->shortName]['college'] = ($obs->startTime > $migrationDate) ? $course->college : '';
                        $orgs[$course->shortName]['department'] = ($obs->startTime > $migrationDate) ? $course->department : '';
                    }
              
                    // create a dummy semester in case it gets deleted from the system
                    if (!($semester = $semSchema->findOne($semSchema->startDate->equals($course->startDate)))) {
                        $semesterDate = (clone $course->startDate)->modify('+2 weeks');
                        $semesterCode = Syllabus_Semesters_Semester::guessActiveSemester(true, $semesterDate, $course->endDate);
                        $semester = new stdClass;
                        $semester->display = Syllabus_Semesters_Semester::ConvertToDescription($semesterCode);
                    }

                    $obsData[$obs->id] = array();
                    $obsData[$obs->id]['obsId'] = $obs->id;
                    $obsData[$obs->id]['course'] = $course->shortName;
                    $obsData[$obs->id]['semester'] = $semester->display;
                    $obsData[$obs->id]['college'] = $orgs[$course->shortName]['college'];
                    $obsData[$obs->id]['department'] = $orgs[$course->shortName]['department'];
                    $obsData[$obs->id]['firstName'] = $obs->account->firstName;
                    $obsData[$obs->id]['lastName'] = $obs->account->lastName;
                    $obsData[$obs->id]['username'] = $obs->account->username;
                    $obsData[$obs->id]['email'] = $obs->account->emailAddress;
                    $obsData[$obs->id]['duration'] = $obs->duration ?? 0;                
                }
            }

            header("Content-Type: application/download\n");
            header('Content-Disposition: attachment; filename="' .$filename. '"' . "\n");
            $handle = fopen('php://output', 'w+');

            if ($handle)
            {
                $headers = array(
                    'Semester',
                    'Department',
                    'Course Short Name',
                    'First Name',
                    'Last Name',
                    'Student ID',
                    'Email',
                    'Duration (minutes)'
                );
                fputcsv($handle, $headers);

                foreach ($obsData as $obs)
                {
                    $row = array(
                        $obs['semester'],
                        $obs['department'],
                        $obs['course'],
                        $obs['firstName'],
                        $obs['lastName'],
                        $obs['username'],
                        $obs['email'],
                        $obs['duration'],
                    );
                    fputcsv($handle, $row);
                }
            }
            
            exit;
        }

        $this->template->tomorrow = $tomorrow;
    }



    public function updateEmailAttachments ($attachmentData)
    {
        $files = $this->schema('Syllabus_Admin_File');
        $attachedFiles = array();

        foreach ($attachmentData as $emailKey => $fileIds)
        {
            foreach ($fileIds as $fileId)
            {
                if (!isset($attachedFiles[$fileId]))
                {
                    $attachedFiles[$fileId] = array();
                }
                if (!in_array($emailKey, $attachedFiles[$fileId]))
                {
                    $attachedFiles[$fileId][] = $emailKey;
                }
            }
        }

        // make sure each file matches the state of posted data
        foreach ($files->getAll() as $file)
        {
            if (!in_array($file->id, array_keys($attachedFiles)))
            {
                $file->attachedEmailKeys = array();
            }
            else // make sure all the files match the posted data
            {
                $file->attachedEmailKeys = $attachedFiles[$file->id];
            }
            $file->save();
        }
    }

    public function emailSettings ()
    {
        $siteSettings = $this->getApplication()->siteSettings;

        if ($this->request->wasPostedByUser())
        {
            switch ($this->getPostCommand()) {
                case 'save':
                    $testing = $this->request->getPostParameter('testingOnly');
                    $testingOnly = ((is_null($testing) || $testing === 0) ? 0 : 1);
                    $siteSettings->setProperty('email-testing-only', $testingOnly);
                    $siteSettings->setProperty('email-test-address', $this->request->getPostParameter('testAddress'));
                    $siteSettings->setProperty('email-default-address', $this->request->getPostParameter('defaultAddress'));
                    $siteSettings->setProperty('email-signature', $this->request->getPostParameter('signature'));
                    $this->response->redirect('admin/settings/email');
                    exit;
            }
        }

        $this->template->authZ = $this->getApplication()->authorizationManager;
        $this->template->testingOnly = $siteSettings->getProperty('email-testing-only', 0);
        $this->template->testAddress = $siteSettings->getProperty('email-test-address');
        $this->template->defaultAddress = $siteSettings->getProperty('email-default-address');
        $this->template->signature = $siteSettings->getProperty('email-signature');
    }
    
    /**
     */
    public function colophon ()
    {
        $moduleManager = $this->getApplication()->moduleManager;
        $this->template->moduleList = $moduleManager->getModules();
    }


}


