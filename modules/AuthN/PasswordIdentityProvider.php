<?php

/**
 */
class Syllabus_AuthN_PasswordIdentityProvider extends Bss_PasswordAuthentication_IdentityProvider
{
	public function hasSingleSignOut () { parent::hasSingleSignOut(); }

    public function getAccountSettingsTemplate ()
    {
        return $this->getModule()->getResource('_password.html.tpl');
    }

    public function getIdentity (Bss_Core_IRequest $request) { parent::getIdentity($request); }

    protected function configureProvider ($attributeMap) { parent::configureProvider($attributeMap); }
}