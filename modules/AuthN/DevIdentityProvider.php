<?php

/**
 * This is only for Dev purposes.
 */
class Syllabus_AuthN_DevIdentityProvider extends Bss_PasswordAuthentication_IdentityProvider
{
    public function hasSingleSignOut () { parent::hasSingleSignOut(); }

    public function getAccountSettingsTemplate ()
    {
        return $this->getModule()->getResource('_password.html.tpl');
    }

    public function getIdentity (Bss_Core_IRequest $request) { return parent::getIdentity($request); }

    protected function configureProvider ($attributeMap) { parent::configureProvider($attributeMap); }

    protected function initializeIdentityProperties (Bss_Core_IRequest $request, Bss_AuthN_Identity $identity)
    {
        parent::initializeIdentityProperties($request, $identity);

        $identity->setProperty('allowCreateAccount', true);
    }
}
