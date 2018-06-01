<?php

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

    protected function flash ($content) {
        $session = $this->request->getSession();
        $session->flashContent = $content;
    }

    // protected function beforeCallback ($callback)
    // {
    //     $this->template->pAdmin = $this->hasPermission('admin');
    // }

    protected function afterCallback ($callback)
    {
        $this->template->pAdmin = $this->hasPermission('admin');
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

        parent::afterCallback($callback);
    }

    public function userMessage ($primary, $details = null)
    {
        $this->userMessageList[] = array(
            'primary' => $primary,
            'details' => (array) $details,
        );
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
