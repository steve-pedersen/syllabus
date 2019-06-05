<?php

/**
 */
class Syllabus_AcademicOrganizations_CollegeController extends Syllabus_Organizations_BaseController
{
    public static function getRouteMap ()
    {
        return [
        	'/organizations'           	=> ['callback' => 'myOrganizations'],
        	'/colleges'             	=> ['callback' => 'listOrganizations'],
        	'/colleges/create'      	=> ['callback' => 'create'],
        	'/colleges/:oid'		   	=> ['callback' => 'dashboard', ':oid' => '[0-9]+'],
        	'/colleges/:oid/manage'  	=> ['callback' => 'manageOrganization', ':oid' => '[0-9]+'],
        	'/colleges/:oid/users'   	=> ['callback' => 'manageUsers', ':oid' => '[0-9]+'],
            '/colleges/:oid/users/:uid' => ['callback' => 'editUser', ':oid' => '[0-9]+'],
        ];
    }

    // public function beforeCallback($callback)
    // {
    // 	parent::beforeCallback($callback);
    // 	parent::$callback();
    // }

	public function getOrganization ($id=null)
	{
		return $this->organizationSchema->get($id) ?? $this->organizationSchema->createInstance();
	}

    public function getOrganizationSchema () { return $this->schema('Syllabus_AcademicOrganizations_College'); }

}