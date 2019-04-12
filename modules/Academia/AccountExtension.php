<?php

/**
 * Adds properties/methods to accounts.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Academia_AccountExtension extends Bss_AuthN_AccountExtension implements Bss_AuthN_IAccountSettingsExtension
{
    private $request;
    private $response;
    
    /**
     * Get all properties to add to an account.
     * 
     * @return array
     */
    public function getExtensionProperties ()
    {
        return array(
            'entity_roles' => array('1:N', 
                'to' => 'Syllabus_AuthN_Role', 
                'via' => 'syllabus_authn_account_entity_roles', 
                'fromPrefix' => 'entity', 
                'toPrefix' => 'role',
                'properties' => array(
                    'id' => 'int', 
                    'name' => 'string', 
                    'entity_id' => 'string', 
                    'creation_date' => 'datetime'
                ),
            )       
        );
    }
 
    public function getSubjectProxies ($account)
    {
        return array();
        return $account->entity_roles->asArray();
    }
    
    /**
     * Get the methods to add to instances of the account class.
     * 
     * @return array
     */
    public function getExtensionMethods ()
    {
        return array('handleSettings');
    }
        
     /**
     * Get the weight of these settings, which determines their order in
     * the form. A heavier item always comes after a lighter item. Two
     * items of the same weight are presented in the order they are
     * loaded, which may vary.
     * 
     * @return int
     */
    public function getAccountSettingsWeight ()
    {
        return 10;
    }
    
    /**
     * Get the path to a template file for rendering as part of the
     * account settings form. May return null if this extension does not
     * render any settings (the extension's processAccountSettings method
     * will still be called).
     * 
     * @return string
     */
    public function getAccountSettingsTemplate ()
    {
        return $this->getModule()->getResource('_settings.html.tpl');
    }
    
    /**
     * Called when the settings form is submitted with the request that
     * submitted the form and the account instance for which the settings
     * are being modified.
     * 
     * @param Bss_Core_IRequest $request
     *    The request that has submitted the form.
     * @param Bss_AuthN_Account $account
     *    The account for which the settings have been submitted.
     * @param array& $errorMap
     *    A reference to an associative array mapping field names to arrays of
     *    error messages related to that field. This method will modify this
     *    error map with any errors that it causes to be set. If any errors are
     *    set in the error map, this method must return false.
     * @return bool
     *    True if the submission did not contain any errors for this settings
     *    extension. Else false. If any errors are set into the error map, this
     *    method must return false.
     */
    public function processAccountSettings (Bss_AuthZ_IParticipant $viewer, Bss_Core_IRequest $request, Bss_AuthN_Account $account, &$errorMap)
    {
        $authZ = $this->getApplication()->authorizationManager;
       
        return true;
    }
    
    public function getAccountSettingsTemplateVariables (Bss_Routing_Handler $handler)
    {

    }

    public function initializeRecord (Bss_ActiveRecord_Base $account)
    {
        $account->addEventHandler('before-delete', array($this, 'deleteAccount'));
    }
    
    public function deleteAccount (Bss_ActiveRecord_Base $account)
    {
        $account->roles->removeAll();
        $account->roles->save();
        $account->entity_roles->removeAll();
        $account->entity_roles->save();
        $account->enrollments->removeAll();
        $account->enrollments->save();
    }


    // public function sendNewAccountNotification ($user, $request)
    // {
    //     $app = $this->getApplication();
    //     $emailManager = new Syllabus_Admin_EmailManager($app);
    //     $emailManager->setTemplateInstance($this->createTemplateInstance($app, $request));

    //     $emailData = array();        
    //     $emailData['user'] = $user;
    //     $emailManager->processEmail('sendNewAccount', $emailData);
    // }


    public function createTemplateInstance ($app, $request)
    {
        $tplClass = $this->getTemplateClass();
        $response = new Bss_Core_Response($request);
        
        $this->request = $request;
        $this->response = $response;
        $inst = new $tplClass ($this, $this->request, $this->response);

        return $inst;
    }

    protected function getTemplateClass ()
    {
        return 'Syllabus_Master_Template';
    }

    public function getUserContext ()
    {
        return new Syllabus_Master_UserContext($this->request, $this->response);
    }
    


}
