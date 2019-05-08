<?php

/**
 */
class Syllabus_AcademicOrganizations_DepartmentController extends Syllabus_Organizations_BaseController
{
    public static function getRouteMap ()
    {
        return [
        	'/departments'             => ['callback' => 'listOrganizations'],
        	'/departments/create'      => ['callback' => 'create'],
        	'/departments/:oid'		   => ['callback' => 'dashboard', ':oid' => '[0-9]'],
        	'/departments/:oid/manage'  => ['callback' => 'manageOrganization', ':oid' => '[0-9]'],
            '/departments/:oid/settings' => ['callback' => 'manageOrganization', ':oid' => '[0-9]'],
        	'/departments/:oid/users'   => ['callback' => 'manageUsers', ':oid' => '[0-9]'],
            '/departments/:oid/users/:uid' => ['callback' => 'editUser', ':oid' => '[0-9]'],
        ];
    }

    public function beforeCallback($callback)
    {
        parent::beforeCallback($callback);
        parent::$callback();
    }

	public function getOrganization ($id=null)
	{
		return $this->organizationSchema->get($id) ?? $this->organizationSchema->createInstance();
	}

    public function getOrganizationSchema () { return $this->schema($this->schemaName); }

    public function getSchemaName () { return 'Syllabus_AcademicOrganizations_Department'; }
}