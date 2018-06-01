<?php

class Syllabus_Welcome_AdminController extends At_Admin_Controller
{
    public static function getRouteMap ()
    {
        return array(
            'admin/welcome' => array('callback' => 'adminWelcome'),
        );
    }

	public function beforeCallback ($callback)
	{
		parent::beforeCallback($callback);
		$this->requirePermission('admin');
	}
    
    public function adminWelcome ()
    {
        $siteSettings = $this->getApplication()->siteSettings;
        
        if ($this->getPostCommand() == 'save' && $this->request->wasPostedByUser())
        {           
            if ($welcomeText = $this->request->getPostParameter('welcome-text'))
            {
                $siteSettings->setProperty('welcome-text', $welcomeText);
                $this->flash('The welcome text has been saved.');
                $this->response->redirect('admin/welcome');
            }
        }
        
        if ($welcomeText = $siteSettings->getProperty('welcome-text'))
        {
            $this->template->welcomeText = $welcomeText;
        }
        
        $this->template->chosenModuleId = $siteSettings->getProperty('welcome-module');
    }
}