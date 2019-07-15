<?php

/**
 * The welcome (landing) page.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Welcome_Controller extends Syllabus_Master_Controller
{
    public static function getRouteMap ()
    {
        return array(
            // '/' => array('callback' => 'welcome'),
            // '/home' => array('callback' => 'welcome'),
        );
    }
    
    public function welcome ()
    {
        $app = $this->getApplication();
        $siteSettings = $app->siteSettings;
        $moduleManager = $app->moduleManager;

        // $welcomeHeroPartial = 'partial:_welcomeHero.html.tpl';
        // $this->template->registerResource('partial', new Bss_Template_PartialResource($this));
        // $this->template->headerPartial = $welcomeHeroPartial;

        if ($welcomeText = $siteSettings->getProperty('welcome-text'))
        {
            $this->template->welcomeText = $welcomeText;
        }        
    }
}
