<?php

class Syllabus_Welcome_AdminController extends Syllabus_Master_AdminController
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
            $welcomeText = $this->request->getPostParameter('welcome-text');
            $welcomeText2 = $this->request->getPostParameter('welcome-text2'); 
            if ($welcomeText || $welcomeText2)
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