<?php

/**
 */
abstract class Syllabus_Organizations_BaseController extends Syllabus_Master_Controller
{
    public static function getRouteMap ()
    {
        return [
            'organizations'        => ['callback' => 'myOrganizations'],
            '/:organizations'       => ['callback' => 'listOrganizations', ':organizations' => 'departments|colleges|groups'],
            '/:organizations/:id'   => ['callback' => 'dashboard', ':organizations' => 'departments|colleges|groups', ':id' => '[0-9]+'],
            '/organizations/admin/import' => ['callback' => 'import'],
        ];
    }

	abstract public function getOrganization ($id=null);

    public function import ()
    {
        $this->requirePermission('admin');
        $this->addBreadcrumb('admin', 'Admin');

        if ($this->request->wasPostedByUser())
        {
            $service = new Syllabus_ClassData_Service($this->application);
            $service->importOrganizations();

            $departments = $this->schema('Syllabus_AcademicOrganizations_Department')->getAll();
            $colleges = $this->schema('Syllabus_AcademicOrganizations_College')->getAll();
            $courseSections = $this->schema('Syllabus_ClassData_CourseSection')->getAll();
            // TODO: Update this to use only users who have a AuthN_Account
            $users = $this->schema('Syllabus_ClassData_User')->getAll();

            foreach ($courseSections as $courseSection)
            {
                echo "<pre>"; var_dump($courseSection->getInstructors()); die;
            }
        }
    }

    public function myOrganizations ()
    {
        $this->buildHeader('partial:_header.edit.html.tpl', 'My Organizations', '', '');
    }

    public function listOrganizations ()
    {
    	$this->template->organization = $this->getOrganization();
    }

    public function dashboard ()
    {
        $this->template->organization = $this->getOrganization();
    }

    public function manageOrganization ()
    {

    }

    public function manageUsers ()
    {

    }
}