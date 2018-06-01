<?php

/**
 * The welcome (landing) page.
 * 
 * @author      Daniel A. Koepke <dkoepke@sfsu.edu>
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Welcome_Controller extends Syllabus_Master_Controller
{
    public static function getRouteMap ()
    {
        return array(
            '/' => array('callback' => 'welcome'),
            '/home' => array('callback' => 'welcome'),
        );
    }
    
    public function welcome ()
    {
        $siteSettings = $this->getApplication()->siteSettings;

        if ($welcomeText = $siteSettings->getProperty('welcome-text'))
        {
            $this->template->welcomeText = $welcomeText;
        }
        
        // if ($account = $this->getAccount())
        // {
        //     $this->response->redirect('/');
        // }
    }
}
