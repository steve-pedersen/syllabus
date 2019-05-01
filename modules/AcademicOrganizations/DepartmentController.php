<?php

/**
 */
class Syllabus_AcademicOrganizations_DepartmentController extends Syllabus_Organizations_BaseController
{
    public static function getRouteMap ()
    {
        return [
            'organizations'             => ['callback' => 'myOrganizations'],
            'organizations/admin/import'=> ['callback' => 'import'],
        	'departments'				=> ['callback' => 'listOrganizations'],
        	'departments/create'		=> ['callback' => 'create'],
        	'departments/:id'			=> ['callback' => 'dashboard', ':id' => '[0-9]'],
        	'departments/:id/manage'	=> ['callback' => 'manageOrganization', ':id' => '[0-9]'],
        	'departments/:id/users'		=> ['callback' => 'manageUsers', ':id' => '[0-9]'],
        ];
    }

    public function listOrganizations ()
    {
    	
    }

    // public function dashboard ()
    // {
    // 	$viewer = $this->requireLogin();
    // 	$department = $this->helper('activeRecord')->fromRoute('Syllabus_AcademicOrganizations_Department', 'id');
    // 	$this->template->organization = $department;
    // }

    public function myOrganizations ()
    {
        parent::myOrganizations();

        // $this->template->departments = ;
    }

    public function import ()
    {
        parent::import();

        // $this->template->departments = ;
    }

    public function create ()
   	{
    	$viewer = $this->requireLogin();
    	$this->requirePermission('manage org');

   		$department = $this->schema('Syllabus_AcademicOrganizations_Department')->createInstance();
   		$this->template->organization = $department;
   	}

    public function manageOrganization ()
    {
    	$viewer = $this->requireLogin();
    	$this->requirePermission('manage org');

    	$department = $this->helper('activeRecord')->fromRoute('Syllabus_AcademicOrganizations_Department', 'id');
    	$this->template->organization = $department;
    }

    public function manageUsers ()
    {
    	$viewer = $this->requireLogin();
    	$this->requirePermission('manage org users');

    	$department = $this->helper('activeRecord')->fromRoute('Syllabus_AcademicOrganizations_Department', 'id');
    	$this->template->organization = $department;
    }


	public function getOrganization ($id=null)
	{
        $schema = $this->schema('Syllabus_AcademicOrganizations_Department');
		return $schema->get($id) ?? $schema->createInstance();
	}
}