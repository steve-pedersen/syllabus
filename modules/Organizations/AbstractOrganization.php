<?php

/**
 * A class to handle the connections and shared properties among College, Dept., and Group models.
 * Each concrete Org shall define the permission sets that define each role.
 *
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   copyright (c) San Francisco State University
 */
abstract class Syllabus_Organizations_AbstractOrganization extends Bss_ActiveRecord_BaseWithAuthorization
{
    private $_members;
    private $_communicators;
    private $_creators;
    private $_repositoryManagers;
    private $_moderators;
    private $_managers;

    abstract public function getOrgType ();
    abstract public function getUserRoles (Bss_AuthN_Account $user);

    public function getMembers ($reload=false)
    {
        if ($reload)
        {
            $userAzids = $authZ->getSubjectsWhoCan('view org templates', $this);
            $this->_members = $this->getSchema('Bss_AuthN_Account')->getByAzids($userAzids);
        }
        return $this->_members;
    }
    abstract public function getCommunicators ($reload=false);
    abstract public function getCreators ($reload=false);
    abstract public function getRepositoryManagers ($reload=false);
    abstract public function getModerators ($reload=false);
    public function getManagers ($reload=false)
    {
        if ($reload)
        {
            $userAzids = $authZ->getSubjectsWhoCan('manage org', $this);
            $this->_managers = $this->getSchema('Bss_AuthN_Account')->getByAzids($userAzids);
        }
        return $this->_managers;      
    }

    abstract public function addMembers ($users=[]);
    abstract public function addCommunicators ($users=[]);
    abstract public function addCreators ($users=[]);
    abstract public function addRepositoryManagers ($users=[]);
    abstract public function addModerators ($users=[]);
    abstract public function addManagers ($users=[]);

    public function getManageUsersTemplate ()
    {
		$template = new DivaTemplate;
        $template->setDefaultResourceDirectory(glue_path(dirname(__FILE__), 'resources'));
		$template->setTemplateFile('manageUsers.html.tpl');
		
		return $template;
    }
}