<?php

/**
 */
class Syllabus_AcademicOrganizations_DepartmentController extends Syllabus_Organizations_BaseController
{
    public static function getRouteMap ()
    {
        return [
        	'/departments'             => ['callback' => 'listOrganizations'],
        	'/departments/:oid'		   => ['callback' => 'dashboard', ':oid' => '[0-9]+'],
        	'/departments/:oid/manage'         => ['callback' => 'manageOrganization', ':oid' => '[0-9]+'],
            '/departments/:oid/settings'       => ['callback' => 'manageOrganization', ':oid' => '[0-9]+'],
        	'/departments/:oid/users'          => ['callback' => 'manageUsers', ':oid' => '[0-9]+'],
            '/departments/:oid/users/:uid'     => ['callback' => 'editUser', ':oid' => '[0-9]+'],
            // '/departments/:oid/templates/:tid' => ['callback' => 'editTemplate', ':oid' => '[0-9]+'],
            '/departments/:oid/syllabus/start' => ['callback' => 'start', ':oid' => '[0-9]+'],
            '/departments/:oid/syllabus/:id'   => ['callback' => 'edit', ':oid' => '[0-9]+|new'],
            '/departments/:oid/templates'      => ['callback' => 'listTemplates', ':oid' => '[0-9]+'],
            '/departments/:oid/syllabus/startwith/:id'   => ['callback' => 'startWith', ':oid' => '[0-9]+|new'],
        ];
    }

	public function getOrganization ($id=null)
	{
        return $this->getOrganizationSchema()->get($id) ?? $this->getOrganizationSchema()->createInstance();
	}

    public function start ()
    {
        $department = $this->getOrganization($this->getRouteVariable('oid'));
        $this->forward('syllabus/start', ['organization' => $department, 'routeBase' => 'departments/'.$department->id]);
    }

    public function edit ()
    {
        $department = $this->getOrganization($this->getRouteVariable('oid'));

        $this->forward('syllabus/new', [
            'organization' => $department, 
            'routeBase' => 'departments/' . $department->id,
            'id' => $this->getRouteVariable('id')
        ]);
    }

    public function startWith ()
    {
        $organization = $this->getOrganization($this->getRouteVariable('oid'));
        $syllabusId = $this->getRouteVariable('id');

        if ($organization && $organization->userHasRole($this->requireLogin(), 'creator'))
        {
            $this->forward("syllabus/startwith/$syllabusId", [
                'id' => $syllabusId,
                'routeBase' => 'departments/' . $organization->id,
                'organization' => $organization, 
                'templateAuthorizationId' => $organization->templateAuthorizationId
            ]);            
        }
    }

    public function getOrganizationSchema () { return $this->schema($this->getSchemaName()); }
    
    public function getSchemaName () { return 'Syllabus_AcademicOrganizations_Department'; }
}