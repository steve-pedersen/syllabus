<?php

/**
 * Syllabus application error handler base class.
 * 
 * @author      Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
abstract class Syllabus_Master_ErrorHandler extends Bss_Master_ErrorHandler
{
    protected function setupAuthenticationError ($error)
    {
        $moduleManager = $this->getApplication()->moduleManager;
        
        if (($authN = $moduleManager->getModule('bss:core:authN')))
        {
            $providerManager = $this->getApplication()->identityProviderManager;
            $providerList = $providerManager->getProviders($error->getRequest()->hasQueryParameter('all'));
            $providerIdList = array_keys($providerList);
            $soleProvider = (count($providerList) == 1 ? $providerIdList[0] : null);
            
            if ($soleProvider)
            {
                $this->template->soleProvider = true;
                $this->template->selectedIdentityProvider = $soleProvider;
            }
            elseif (($wayfSettings = $error->getRequest()->getCookie('wayfSettings')) && in_array($wayfSettings, $providerIdList))
            {
                $this->template->selectedIdentityProvider = $wayfSettings;
            }
            
            $this->template->providerList = $providerList;
            $this->addTemplateFileLocatorToEnd(new Bss_Template_ModuleFileLocator($authN));
        }
    }
}
