<?php

/**
 * Create the configuration options.
 * 
 * @author      Daniel A. Koepke (dkoepke@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Welcome_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {
        $siteSettings = $this->getApplication()->siteSettings;
        switch ($fromVersion)
        {
            case 0:
                $siteSettings->defineProperty('welcome-text', 'Text to show on welcome page', 'string');
                break;

            case 1:
                $siteSettings->defineProperty('welcome-text-bottom-column1', 'Text to show on welcome page bottom column 1', 'string');
                $siteSettings->defineProperty('welcome-text-bottom-column2', 'Text to show on welcome page bottom column 2', 'string');
                break;
        }
    }
}