<?php

/**
 * Workstation Selection application error handler for authentication
 * errors. This error can be caused by:
 * 
 * 1. An identity provider returning an identity with an empty username. This
 *    is generally caused by a misconfiguration either of the remote identity
 *    provider (not releasing the expected attributes) or of the attribute
 *    mapping for the application (expecting the attribute to have a name it
 *    doesn't).
 * 2. An identity provider returning an identity it cannot authenticate. This
 *    can happen for identity provider implementations that have a local
 *    component, like PasswordAuthentication and LDAP, where an account can be
 *    found by the username even if the password is wrong.
 * 
 * @author      Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_AuthN_AuthenticationFailureErrorHandler extends Syllabus_Master_ErrorHandler
{
    public static function getErrorClassList () { return array('Bss_AuthN_ExAuthenticationFailure'); }
    
    protected function getStatusCode () { return 403; }
    protected function getStatusMessage () { return 'Forbidden'; }
    protected function getTemplateFile () { return 'error-403-authentication-failure.html.tpl'; }
    
    protected function handleError ($error)
    {
        $this->setupAuthenticationError($error);
        $this->template->identity = $identity = $error->getExtraInfo();
        $this->template->identityProvider = $identity->getIdentityProvider();
        parent::handleError($error);
    }
}
