<?php

/**
 * Handles the case when someone attempts to login with a nonexistent account.
 * There are three different cases of interest here:
 * 
 * 1. The user is authenticated by Shibboleth (or another remote identity
 *    provider) and has a Syllabus account, but their Syllabus account's username
 *    equals their e-mail address rather than the username returned by 
 *    Shibboleth. We fix the acocunt's username and log them in seamlessly.
 * 
 * 2. The user is authenticated but they do not have a Syllabus account with a
 *    username that matches either their Shibboleth username or e-mail
 *    address. It's still possible that the person has a Syllabus account under a
 *    different e-mail address, but we deal with these mismatches through 
 *    manual intervention. If the user is of the appropriate role, they should
 *    be allowed to create a new account. If the user is enrolled as an instructor
 *    via ClassData, give them Faculty Role and add them as members to any
 *    departments and colleges that they should be associated with.
 * 
 * 3. The user is unauthenticated and no account could be found. This generally
 *    occurs because the user entered the wrong username (e-mail address) for
 *    the internal password authentication scheme. We treat this the same as
 *    Bss_AuthN_ExLoginRequired.
 * 
 * @author      Daniel A. Koepke (dkoepke@sfsu.edu)
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_AuthN_NoAccountErrorHandler extends Syllabus_Master_ErrorHandler
{
    public static function getErrorClassList () { return array('Bss_AuthN_ExNoAccount'); }
    
    private $pageTitle;
    
    protected function getStatusCode () { return 403; }
    protected function getStatusMessage () { return 'Forbidden'; }
    protected function getTemplateFile () { return 'error-login.html.tpl'; }
    
    protected function handleError ($error)
    {
        parent::handleError($error);
        
        $identity = $error->getExtraInfo();
        $provider = $identity->getIdentityProvider();
        if ($identity->getAuthenticated())
        {
            // We know the user's identity for sure, but we couldn't find an
            // account for them. This only happens for Shibboleth, LDAP, and
            // similar remote authentication systems, where we can verify
            // the person's identity independent of knowing that they own a
            // particular Syllabus account.
            
            // This situation can occur if the person doesn't have a Syllabus
            // account at all, or if they have an older account where the
            // username is their e-mail address.
            
            if (($username = $identity->getProperty('username')))
            {
                $accounts = $this->schema('Bss_AuthN_Account');
                $account = $accounts->findOne($accounts->username->lower()->equals(strtolower($username)));
                
                if ($account)
                {
                    // The user has an account with username equal to their
                    // e-mail address. We trust that the identity provider
                    // requires the person to prove they own whatever e-mail
                    // address that is being reported to us. (In general,
                    // this is a dangerous assumption, but we can vet that
                    // with our identity providers.)
                    
                    // If we didn't trust them, we'd have to send an e-mail
                    // out to the user with a link to initiate the migration.
                    // Let's go with this for right now.
                    
                    // NOTE: login() is guaranteed to save the account.
                    
                    $account->username = $identity->getProperty('username');
                    $this->getUserContext()->login($account);
                }
            }
            if (($allowCreateAccount = $identity->getProperty('allowCreateAccount')))
            {
                $accountManager = new Syllabus_ClassData_AccountManager($this->getApplication());
                $account = $accountManager->createUserAccount($identity);
                
                $this->getUserContext()->login($account);
                // $this->response->redirect('home');
            }
            else
            {
                $this->template->setPageTitle('Account creation disallowed');
            }
            
            $this->template->identity = $identity;
            $this->template->allowCreateAccount = $allowCreateAccount;
        }
        else
        {
            // Treat it the same as if they had an account but failed to
            // authenticate, which is probably due to them entering the
            // wrong username for the internal password authentication
            // scheme.
            
            $this->forwardError('Bss_AuthN_ExLoginRequired', $error);
        }
    }
    
    private function getAffiliations ($identity)
    {
        if (($affiliationList = $identity->getProperty('affiliation', [])))
        {
            if (is_string($affiliationList))
            {
                $affiliationList = explode(';', $affiliationList);
            }
            
            $affiliationList = array_map(array($this, 'normalizeAffiliation'), $affiliationList);
        }
        
        return $affiliationList;
    }
    
    protected function normalizeAffiliation ($affiliation)
    {
        return strtolower(trim($affiliation));
    }
    
    private function guessUniversity ($identity, $provider)
    {
        $universities = $this->schema('Bss_Academia_University');
        $condList = [];
        
        // If we have an organization from Shibboleth, see if it matches the
        // university abbreviation.
        if (($org = $identity->getProperty('organization')))
        {
            $condList[] = $universities->abbreviation->lower()->equals(strtolower($org));
        }
        
        $providerName = strtolower(preg_replace('/^([^-]+)-shib$/', '\\1', $provider->getName()));
        $condList[] = $universities->abbreviation->lower()->equals($providerName);
        
        $providerTitle = strtolower($provider->getDisplayName());
        $condList[] = $universities->name->lower()->equals($providerTitle);
        
        return $universities->findOne($universities->anyTrue($condList));
    }
}
