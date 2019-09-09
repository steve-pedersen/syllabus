<?php

/**
 * Represents a user of Syllabus application, regardless of whether they are logged in
 * or not.
 * 
 * @author      Stevew Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Master_UserContext extends Bss_Master_UserContext
{
    protected function setAccount ($account)
    {
        parent::setAccount($account);
    }

    public function login (Bss_AuthN_Account $account)
    {
        $firstLogin = ($account->firstLoginDate === null);
        parent::login($account);
        $this->sendRedirect($account, $firstLogin);
    }

    public function sendRedirect ($account, $firstLogin)
    {
        $schemaManager = $this->request->getApplication()->schemaManager;
        $roles = $schemaManager->getSchema('Syllabus_AuthN_Role');
        $authZ = $this->getAuthorizationManager();
        $returnTo = isset($_SESSION['returnTo']) ? $_SESSION['returnTo'] : null;
        
        if ($return = $this->request->getQueryParameter('returnTo', $returnTo))
        {
            $this->response->redirect($return);
        }
        elseif ($authZ->hasPermission($account, 'admin'))
        {
            if ($returnTo) $this->response->redirect($returnTo);
            $this->response->redirect('admin');
        }
        elseif ($account->roles->has($roles->findOne($roles->name->equals('Faculty'))))
        {
            if ($returnTo) $this->response->redirect($returnTo);
            if ($firstLogin)
            {
                $this->response->redirect('syllabi?mode=courses');
            }
            $this->response->redirect('syllabi?mode=overview');
        }
        elseif ($account->roles->has($roles->findOne($roles->name->equals('Student'))))
        {
            if ($returnTo) $this->response->redirect($returnTo);
            $this->response->redirect('syllabi?mode=overview');
        }
        $this->response->redirect('syllabi');        
    }

    public function becomeAccount ($account, $returnTo)
    {
        if ($account)
        {
            $session = $this->request->getSession();
            
            // Switch back to their real user account before becoming another user.
            $this->unbecome();
            
            // Remember their real account and where they used 'become' from.
            $session->wasAccountId = $session->accountId;
            $session->logoutReturnTo = $returnTo;
            
            // And set their account to the new user.
            $this->setAccount($account);
            $this->sendRedirect($account, ($account->firstLoginDate===null));
            return true;
        }
        
        return false;
    }
}
