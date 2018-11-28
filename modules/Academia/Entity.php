<?php

/**
 * A class to handle abstract common functionality for academic entities and concrete ones, 
 * such as College and Department.
 *
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   copyright (c) San Francisco State University
 */
abstract class Syllabus_Academia_Entity extends Bss_ActiveRecord_Base
{
    /**
     * Instance of child class entity (College, Dept., Group, Course)
     *
     * @return ActiveRecord
     */
	abstract public function getConcreteEntity ();

    /**
     * Should return get_class($this)
     *
     * @return string
     */
    // abstract public function getClassName ();
    
    // abstract public function getHref ();

    public function removeUser ($account)
    {
        // TODO: maybe instead of $permissions it should be $entityRoles
        //  For example, $account->entityRoles->removeAll()
        //  This seems correct since the EntityRoles will have the permissions and the account
        //  has the EntityRoles.
        $permissions = array('read', 'write', 'edit', 'delete', 'edit settings', 'manage users');
    	$authZ = $this->getApplication()->authorizationManager;
        foreach ($permissions as $permission)
        {
            $authZ->revokePermission($account, 'entity '. $permission, $this->entity);
        }
    }
	
    // TODO: maybe instead of $permissions it should be $entityRoles
	public function addUsers ($users, $permissions=array())
	{
		$authZ = $this->getApplication()->authorizationManager;

		foreach ($users as $user)
		{
            // $user->isActive = true;
            $user->save();
           
            $authZ->grantPermission($user, 'entity read', $this->entity, false);
            
            if ($permissions)
            {
                foreach ($permissions as $permission)
                {
                    $authZ->grantPermission($user, 'entity ' . trim($permission), $this->entity, false);
                }
            }
		}
		$authZ->updateCache();
	}  
    
    // public function userIsMember ($account)
    // {
    //     return $account->hasPermission('entity member', $this->entity);
    // }
    // public function userIsContributor ($account)
    // {
    //     return $account->hasPermission('entity contributor', $this->entity);
    // }
    // public function userIsModerator ($account)
    // {
    //     return $account->hasPermission('entity moderator', $this->entity);
    // }
    // public function userIsAdmin ($account)
    // {
    //     return $account->hasPermission('entity admin', $this->entity);
    // }

    public function getName () { return $this->name; }
    public function getDescription () { return $this->description; }

    public function getContactPhone ()
    {
        $value = $this->_fetch('contactPhone');
        
        if (strlen($value) == 10)
        {
            return preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '\1-\2-\3', $value);
        }
        elseif (strlen($value) == 7)
        {
            return preg_replace('/([0-9]{3})([0-9]{4})/', '\1-\2', $value);
        }
        
        return $value;
    }
    
    public function setContactPhone ($value)
    {
        $value = preg_replace('/[^0-9]+/', '', $value);
        
        if (!empty($value))
        {
            if (strlen($value) == 11 && $value[0] == '1')
            {
                // Strip the leading one.
                $value = substr($value, 1);
            }
            
            if (strlen($value) == 7)
            {
                $this->invalidate('contactPhone', 'Please use a full, 10-digit telephone number, including the area code.');
            }
            elseif (strlen($value) < 10)
            {
                $this->invalidate('contactPhone', 'Please use a full, 10-digit telephone number (for example, <samp>415-555-1234</samp>).');
            }
            elseif (strlen($value) > 10)
            {
                $this->invalidate('contactPhone', 'Please use a 10-digit US telephone number (for example, <samp>415-555-1234</samp>). We do not currently support international or other non-US telephone numbers.');
            }
        }
        
        $this->_assign('contactPhone', $value);
    }

    // TODO: Test this stuff
    protected function beforeUpdate ()
    {
        parent::beforeUpdate();
        // $entity = $this->getSchema('Syllabus_Academia_Entity')->get($this->id);
        $entity = $this->getConcreteEntity();
        $entity->modifiedDate = new DateTime;
        $entity->save();
    }
    
    protected function beforeDelete ()
    {
        parent::beforeDelete();
        
        if ($this->entity) 
        {
            $this->entity->delete();
        }
    }
}
