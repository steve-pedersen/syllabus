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
			'/admin/apc' => ['callback' => 'clearMemoryCache'],
            '/admin/cron' => ['callback' => 'cron'],
            '/admin/settings/siteNotice' => ['callback' => 'siteNotice'],
            '/admin/semesters' => ['callback' => 'editSemesters'],
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

}
