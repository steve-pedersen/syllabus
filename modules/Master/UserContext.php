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
        
        // if ($account)
        // {
        //     if ($this->getAuthorizationManager()->hasPermission($account, 'admin'))
        //     {
        //         $this->response->redirect('admin');
        //     }
        //     else // if not admin, go to 
        //     {
                 
        //         if ($return = $this->request->getQueryParameter('returnTo'))
        //         {         
        //             $this->response->redirect($return);
        //         }
        //         elseif (false) // has some other special permission
        //         {
        //             // $this->response->redirect('special/homepage');
        //         }
        //         else
        //         {

        //             $this->response->redirect('/');
        //         }
        //     }
        // }
    }

    /**
     * Login as the specified account.
     * 
     * This method sets the account's last and (if it's unset) first login
     * timestamps, and saves any changes that have been made to the account
     * and the active records it references.
     * 
     * @param Bss_AuthN_Account $account
     */
    // public function login (Bss_AuthN_Account $account)
    // {
    //     $account->lastLoginDate = new DateTime;
        
    //     $firstLogin = false;
    //     if ($account->firstLoginDate === null)
    //     {
    //         $firstLogin = true;
    //         $account->firstLoginDate = $account->lastLoginDate;
    //     }
        
    //     $account->save();
    //     $this->setAccount($account);

    //     $schemaManager = $this->request->getApplication()->schemaManager;
    //     $roles = $schemaManager->getSchema('Bss_AuthN_Account');
    //     $authZ = $this->getAuthorizationManager();
    //     if ($authZ->hasPermission($account, 'admin'))
    //     {
    //         $this->response->redirect('admin');
    //     }
    //     elseif ($account->roles->has($roles->findOne($roles->name->equals('Faculty'))))
    //     {
    //         $this->response->redirect('arc');
    //     }
    //     elseif ($authZ->hasPermission($account, 'grad advisor'))
    //     {
    //         $this->response->redirect('grad');
    //     }
    //     $this->response->redirect('arc');
    // }
}
