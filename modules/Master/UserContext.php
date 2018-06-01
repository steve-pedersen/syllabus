<?php

/**
 * Represents a user of Syllabus application, regardless of whether they are logged in
 * or not.
 * 
 * @author      Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Master_UserContext extends Bss_Master_UserContext
{
    protected function setAccount ($account)
    {
        parent::setAccount($account);
        
        if ($account)
        {
            if ($this->getAuthorizationManager()->hasPermission($account, 'admin'))
            {
                $this->response->redirect('admin');
            }
            else // if not admin, go to 
            {
                 
                if ($return = $this->request->getQueryParameter('returnTo'))
                {         
                    $this->response->redirect($return);
                }
                elseif (false) // has some other special permission
                {
                    // $this->response->redirect('special/homepage');
                }
                else
                {

                    $this->response->redirect('/');
                }
            }
        }
    }
}
