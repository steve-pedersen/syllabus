<?php

/**
 * Syllabus application error handler for 404 errors.
 * 
 * @author      Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Master_NotFoundErrorHandler extends Syllabus_Master_ErrorHandler
{
    public static function getErrorClassList () { return array('Bss_Routing_ExNotFound'); }
    
    protected function getStatusCode () { return 404; }
    protected function getStatusMessage () { return 'Not Found'; }
    protected function getTemplateFile () { return 'error-404.html.tpl'; }

    protected function handleError ($error)
    {
        $identity = $error->getExtraInfo();
        
        if ($identity && !$identity->getAuthenticated())
        {
            // To avoid leaking information, we only handle NoAccount if the
            // identity provider has authenticated the identity (i.e., the
            // person is who they say they are, they just don't have an
            // account).
            
            // Specifically, for the PasswordAuthentication system, this means
            // that the error page is the same if someone enters a non-existent
            // username AND if someone enters an existing username with the
            // wrong password.
            
            $this->forwardError('Bss_AuthN_ExAuthenticationFailure', $error);
        }
        
        
        // If you want to allow an identity provider to create accounts, here's
        // where you'd put your code.
        
        $this->template->identity = $identity;
        $this->template->identityProvider = $identity->getIdentityProvider();
        parent::handleError($error);
    }
}
