<?php

require_once Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', 'traits', 'IsNotWhiteSpaceOnly.php');

/**
 * The master controller for Syllabus application. Put basic functionality that you want
 * the other controllers in your application to have available to them here.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
abstract class Syllabus_Master_Controller extends Bss_Master_Controller
{

    private $userContext;
    private $onLoadScriptList;
    private $includeScriptList;
    private $userMessageList;
    private $pageTitle = array();

    protected function getTemplateClass () { return 'Syllabus_Master_Template'; }

    protected function getThis () { return $this; }

    protected function initController ()
    {
        parent::initController();
        
        // Initialize data members here.
        $this->template->userContext = $this->getUserContext();
        $viewer = $this->getAccount();
        $app = $this->getApplication();
        $this->template->viewer = $viewer;

        $authZ = $this->getAuthorizationManager();
        $authZ->addSource('session',
            new Bss_AuthZ_SessionPermissionSource($authZ, array(
                'session' => $this->request->getSession(),
            ))
        );
        
        // If you want to setup template variables, it's recommended to do that
        // in Syllabus_Master_Template. If you setup template variables
        // here (instead of your template class), they will not be set in
        // framework controllers.
    }

    protected function flash ($content, $class='success') {
        $session = $this->request->getSession();
        $session->flashContent = $content;
        $session->flashClass = $class;
    }

    protected function printData ($data, $die = true)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";

        if ($die) die;
    }

    protected function afterCallback ($callback)
    {
        $this->template->ctrl = $this;
        $this->template->pAdmin = $this->hasPermission('admin');
        $this->template->pFaculty = $this->hasPermission('syllabus edit');
        $this->template->pProgramAdmin = $this->hasPermission('program admin');
        $this->template->onLoad = $this->onLoadScriptList;
        $this->template->userMessageList = $this->userMessageList;
        $this->template->includeScripts = $this->includeScriptList;
        $this->template->analyticsCode = $this->getApplication()->configuration->getProperty('analyticsTrackingCode');
        $this->template->setPageTitle(!empty($this->pageTitle) ? implode(' - ', $this->pageTitle) : '');

        $session = $this->request->getSession();
        if (isset($session->flashContent))
        {
            $this->template->flashContent = $session->flashContent;
            unset($session->flashContent);
        }
        if (isset($session->flashClass))
        {
            $this->template->flashClass = $session->flashClass;
            unset($session->flashClass);
        }
        
        $page = '';
        if ($callback === 'mySyllabi' || $callback === 'start')
        {
            if (($mode = $this->request->getQueryParameter('mode')) && $this->request->getQueryParameter('mode') !== '')
            {
                $page = $mode;
            }
            elseif ($callback === 'start')
            {
                $page = 'start';
            }
            else 
            {
                $page = 'overview';
            }
        }
        elseif ($callback === 'listOrganizations')
        {
            $uri = $this->request->getFullRequestedUri();
            if (strpos($uri, '/syllabus/') !== false)
            {
                $uri = substr($uri, strpos($uri, '/', 1)+1, strlen($uri));
            }
            $page = $uri;
        }
        elseif ($callback === 'migrate')
        {
            $page = 'migrate';
        }
        
        $this->template->page = $page;

        $roles = $this->schema('Syllabus_AuthN_Role');
        if (!$this->request->getQueryParameter('token') && $callback !== 'screenshot' && 
            $callback !== 'export' && $callback !== 'ping')
        {
            $studentRole = $roles->findOne($roles->name->equals('Student'));
            $this->template->isStudent = $this->getAccount()->roles->has($studentRole);
            $this->template->activeSemester = $this->getActiveSemester();
            $this->template->privilegedOrganizations = $this->getPrivelegedUserOrganizations();
        }
        
        if ($viewer = $this->getAccount())
        {
            $facultyRole = $roles->findOne($roles->name->equals('Faculty'));
            if ($viewer->roles->has($facultyRole))
            {
                $this->template->hasProfile = true;
            }
        }

        parent::afterCallback($callback);
    }

    public function getActiveSemester ()
    {
        $today = new DateTime;
        $schema = $this->schema('Syllabus_Admin_Semester');
        $activeSemester = $schema->findOne(
            $schema->endDate->after($today), ['orderBy' => '+startDate']
        );

        return $activeSemester;
    }

    public function getPrivelegedUserOrganizations ()
    {
        $viewer = $this->requireLogin();
        $orgs = [];
        $orgs['departments'] = [];
        $orgs['colleges'] = [];

        foreach ($viewer->classDataUser->enrollments as $cs)
        {
            if ($cs->department)
            {
                if (!isset($orgs['departments'][$cs->department->id]) && $cs->department->userIsMoreThanMember($viewer))
                {
                    $orgs['departments'][$cs->department->id] = $cs->department;
                }                
            }

            // if (!isset($orgs['colleges'][$cs->department->college->id]) && $cs->department->college->userIsMoreThanMember($viewer))
            // {
            //     $orgs['colleges'][$cs->department->college->id] = $cs->department->college;
            // }
        }
        // echo "<pre>"; var_dump(count($orgs['departments'])); die;
        return $orgs;
    }

    protected function beforeCallback ($callback)
    {
        parent::beforeCallback($callback);
        $this->template->pFaculty = $this->hasPermission('syllabus edit');
        $this->template->pProgramAdmin = $this->hasPermission('program admin');
    }

    // TODO: put this in ActiveRecord?
    // TODO: update for multiple syllabusIds requests at a time
    public function getScreenshotUrl ($syllabusId, $screenshotter=null, $cacheImages=true)
    {
        $viewer = $this->requireLogin();
        $syllabus = $this->schema('Syllabus_Syllabus_Syllabus')->get($syllabusId);
        $urls = [];
        $messages = [];
        $uid = $viewer->id;
        $uid = sha1($syllabusId);
        $checkFailCache = false;

        if ($checkFailCache)
        {
            if ($this->cacheFail($syllabusId, true))
            {
                $results = new stdClass;
                $results->imageUrls = new stdClass;
                $results->imageUrls->$syllabusId = 'assets/images/testing01.jpg';                         
            }
            else
            {
                $keyPrefix = "{$uid}-";
                $screenshotter = $screenshotter ?? new Syllabus_Services_Screenshotter($this->getApplication());
                $screenshotter->saveUids($uid, $syllabus->id);

                $urls[$syllabus->id] = $this->baseUrl("syllabus/{$syllabus->id}/screenshot");
                $results = $screenshotter->concurrentRequests($urls, $cacheImages, $keyPrefix);
                $results = json_decode($results);

                if (isset($results->messages) && $results->messages !== '' && $results->messages !== [])         
                {
                    $results->imageUrls->$syllabusId = 'assets/images/testing01.jpg';
                    $this->cacheFail($syllabusId);
                }
            }
        }
        else
        {
            $sid = $syllabus->id ?? '';
            $keyPrefix = "{$uid}-";
            $screenshotter = $screenshotter ?? new Syllabus_Services_Screenshotter($this->getApplication());
            $screenshotter->saveUids($uid, $sid);

            $urls[$sid] = $this->baseUrl("syllabus/{$sid}/screenshot");
            $results = $screenshotter->concurrentRequests($urls, $cacheImages, $keyPrefix);
            $results = json_decode($results);
        }

        return $results;
    }

    private function cacheFail ($sid, $checkCached=false)
    {
        $cookieName = 'syllabus-screenshot-fail-'.$sid;
        $cookieValue = $sid;

        if ($checkCached && isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] !== $cookieValue)
        {
            return true;
        }
        setcookie($cookieName, $cookieValue, time()+60*60, '/');
    }

    public function buildHeader ($file, $title='', $subtitle='', $description='', $ctrl=null)
    {
        $ctrl = $ctrl ?? $this;
        $partialVars = [
            'varName' => 'headerPartial',
            'fileName' => ($file ?? 'partial:_header.html.tpl'),
            'varKey' => 'headerVars',
            'vars' => [
                'title' => $title,
                'subtitle' => $subtitle,
                'description' => $description,
            ],
        ];
        $this->setPartialTemplate($ctrl, $partialVars);
    }

    public function setPartialTemplate ($controller, $vars)
    {
        $this->template->registerResource('partial', new Bss_Template_PartialResource($controller));
        $key = $vars['varName'];
        $this->template->$key = $vars['fileName'];
        $key = $vars['varKey'];
        $this->template->$key = $vars['vars'];
    }

    public function getDragDropUploadFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_dragDropUpload.html.tpl');
    }

    public function userMessage ($primary, $details = null)
    {
        $this->userMessageList[] = array(
            'primary' => $primary,
            'details' => (array) $details,
        );
    }
    
    public function sendError ($statusCode, $statusMessage, $errorType, $errorMessage)
    {
        $this->response->setStatus($statusCode, $statusMessage);
        $this->response->writeJson(array(
            'error' => $errorType,
            'message' => $errorMessage,
        ));
        exit;
    }

    public function includeScript ($js)
    {
        $this->includeScriptList[] = $js;
    }
    
    public function addLoadScript ($js)
    {
        $this->onLoadScriptList[] = $js;
    }

    public function addToPageTitle ($piece)
    {
        $this->pageTitle[] = $piece;
    }

    public function overridePageTitle ($title)
    {
        $this->pageTitle = (array)$title;
    }
    
    public function getUserContext ()
    {
        if (isset($this->userContext) || !$this->userContext)
        {
            $this->userContext = new Syllabus_Master_UserContext($this->request, $this->response);
        }
        else
        {
            return null;
        }
        
        return $this->userContext;
    }
    
    public function grantPermission ($taskList, $object = Bss_AuthZ_Manager::SYSTEM_ENTITY)
    {
        if (($account = $this->getAccount()))
        {
            $taskList = (array) $taskList;
            $authZ = $this->getAuthorizationManager();
            
            foreach ($taskList as $task)
            {
                $authZ->grantPermission($account, $task, $object, false);
            }
            
            $authZ->updateCache();
            return true;
        }
        
        return false;
    }
    
    public function revokePermission ($taskList, $object = Bss_AuthZ_Manager::SYSTEM_ENTITY)
    {
        if (($account = $this->getAccount()))
        {
            $taskList = (array) $taskList;
            $authZ = $this->getAuthorizationManager();
            
            foreach ($taskList as $task)
            {
                $authZ->revokePermission($account, $task, $object, false);
            }
            
            $authZ->updateCache();
            return true;
        }
        
        return false;
    }
    
    public function getAccount ()
    {
        return $this->getUserContext()->getAccount();
    }
    
    public function requireLogin ()
    {
        if (!($account = $this->getAccount()))
        {
            $this->triggerError('Bss_AuthN_ExLoginRequired');
        }
        
        return $account;
    }

    public function requireExists ($entity, $suggestionList = array())
    {
        if ($entity === null)
        {
            $this->notFound($suggestionList);
        }
        
        return $entity;
    }
    
    public function processSubmission (Bss_ActiveRecord_Base $record, $fieldMap, $paramMap = array())
    {
        $skipIfEmpty = ($paramMap && !empty($paramMap['skipIfEmpty']));
        
        foreach ($fieldMap as $fieldName => $propertyName)
        {
            if (is_numeric($fieldName))
            {
                $fieldName = $propertyName;
            }
            
            $value = $this->request->getPostParameter($fieldName);
            
            if (!$skipIfEmpty || !empty($value))
            {
                $record->setProperty($propertyName, $value);
            }
        }
        
        if (!$this->request->wasPostedByUser())
        {
            $this->userMessage('Form submission out of date.', 'Please resubmit the form to save your changes.');
        }

        $this->template->errorMap = $record->getValidationMessages();
        return $this->request->wasPostedByUser() && $record->isValid();
    }
    
    /**
     */
    public function whoCan ($task, $object = Bss_AuthZ_Manager::SYSTEM_ENTITY, $paramMap = array())
    {
        $recordClass = (isset($paramMap['recordClass']) ? $paramMap['recordClass'] : 'Bss_AuthN_Account');
        
        return $this->schema($recordClass)->getByAzids(
            $this->getAuthorizationManager()->getSubjectsWhoCan($task, $object),
            null, // No additional filtering.
            $paramMap
        );
    }

    public function createEmailTemplate ()
    {
        $template = $this->createTemplateInstance();
        $template->setMasterTemplate(Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', 'email.html.tpl'));
        return $template;
    }
    
    public function setSyllabusTemplate ()
    {
        $this->template->setMasterTemplate(Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', 'syllabus.html.tpl'));
    }

    public function setScreenshotTemplate ()
    {
        $this->template->setMasterTemplate(Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', 'screenshot.html.tpl'));
    }

    public function setPrintTemplate ()
    {
        $this->template->setMasterTemplate(Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', 'print.html.tpl'));
    }

    public function setExportTemplate ()
    {
        $this->template->setMasterTemplate(Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', 'export.html.tpl'));
    }

    public function createEmailMessage ($contentTemplate = null)
    {
        $message = new Bss_Mailer_Message($this->getApplication());
        
        if ($contentTemplate)
        {
            $tpl = $this->createEmailTemplate();
            $message->setTemplate($tpl, $this->getModule()->getResource($contentTemplate));
        }
        
        return $message;
    }
}
