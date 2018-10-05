<?php

/**
 * Administrate services settings
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Services_AdminController extends At_Admin_Controller
{
    public static function getRouteMap ()
    {
        return array(
            'admin/services' => array('callback' => 'index'),
        );
    }
    
    public function index ()
    {
        $siteSettings = $this->getApplication()->siteSettings;
        
        if ($this->getPostCommand() == 'save' && $this->request->wasPostedByUser())
        {
            $siteSettings->setProperty('screenshotter-api-url',$this->request->getPostParameter('screenshotter-api-url'));
            $siteSettings->setProperty('screenshotter-api-key',$this->request->getPostParameter('screenshotter-api-key'));
            $siteSettings->setProperty('screenshotter-api-secret',$this->request->getPostParameter('screenshotter-api-secret'));
            $siteSettings->setProperty('screenshotter-default-img-name',$this->request->getPostParameter('screenshotter-default-img-name'));
            $this->flash('Settings have been saved', 'success');
        }
        
        $this->template->screenshotterApiUrl = $siteSettings->getProperty('screenshotter-api-url');
        $this->template->screenshotterApiKey = $siteSettings->getProperty('screenshotter-api-key');
        $this->template->screenshotterApiSecret = $siteSettings->getProperty('screenshotter-api-secret');
        $this->template->screenshotterDefaultImgName = $siteSettings->getProperty('screenshotter-default-img-name');
        // $this->template->screenshotterDefaultImgNameDescription = $siteSettings->getProperties()['screenshotter-default-img-name']['description'];
    }
}