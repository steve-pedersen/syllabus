<?php

/**
 * 
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Services_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {
        $siteSettings = $this->getApplication()->siteSettings;
        switch ($fromVersion)
        {
            case 0:
                $siteSettings->defineProperty('screenshotter-api-url', 'API URL endpoint for the Screenshotter service.', 'string');
                $siteSettings->defineProperty('screenshotter-api-key', 'API Key for the Screenshotter service.', 'string');
                $siteSettings->defineProperty('screenshotter-api-secret', 'API Secret for the Screenshotter service.', 'string');
                $siteSettings->defineProperty('screenshotter-default-img-name', 
                    'Set the filename of the image to default to for this application, in the event that Screenshotter fails to capture a particular URL.', 
                    'string'
                );
                break;

            case 1:
                $siteSettings->defineProperty('atoffice-api-url', 'API URL endpoint for the AT Office service.', 'string');
                break;
        }
    }
}