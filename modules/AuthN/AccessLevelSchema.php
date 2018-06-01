<?php

/**
 */
class Syllabus_AuthN_AccessLevelSchema extends Bss_ActiveRecord_Schema
{
    private $_private;
    private $_public;
    private $_protected;
	private $_csuRights;
    
    public function getPrivate ()
    {
        if ($this->_private == null)
        {
            $this->_private = $this->getLevel('private', 'Private', 'Only available to those you explicitly invite.');
        }
        
        return $this->_private;
    }
    
    public function getPublic ()
    {
        if ($this->_public == null)
        {
            $this->_public = $this->getLevel('public', 'Public', 'Available to anyone who knows the address.');
        }
        
        return $this->_public;
    }
    
    public function getProtected ()
    {
        if ($this->_protected == null)
        {
            $this->_protected = $this->getLevel('protected', 'Protected', 'Requires a password.');
        }
        
        return $this->_protected;
    }

	public function getCsuRights ()
	{
		if ($this->_csuRights == null)
		{
			$this->_csuRights = $this->getLevel('csuRights', 'CSU Rights', 'Available to anyone at a CSU campus, and CSU faculty/staff when logged in.');
		}
		
		return $this->_csuRights;
	}
    
    private function getLevel ($configKey, $name, $description)
    {
        $accessLevel = null;
        $id = $this->getSchemaManager()->getApplication()->configuration->getProperty('accessLevels.' . $configKey, null);
        
        if ($id)
        {
            $accessLevel = $this->get($id);
        }
        else
        {
            $accessLevel = $this->findByName($name, array('returnOne' => true));
        }
        
        if ($accessLevel == null)
        {
            // Still no access level...
            $this->getSchemaManager()->getApplication()->log('error', 'The ' . $configKey . ' access level is missing -- recreating it.');
            
/*
            $accessLevel = $this->createInstance();
            $accessLevel->name = $name;
            $accessLevel->description = $description;
            $accessLevel->save();
*/
        }
        
        return $accessLevel;
    }
}
