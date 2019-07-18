<?php

/**
 */
class Syllabus_AcademicOrganizations_CollegeController extends Syllabus_Organizations_BaseController
{
    public static function getRouteMap ()
    {
        return [
            '/organizations'                => ['callback' => 'myOrganizations'],
            '/colleges'             => ['callback' => 'listOrganizations'],
            '/colleges/:oid'        => ['callback' => 'dashboard', ':oid' => '[0-9]+'],
            '/colleges/:oid/manage'         => ['callback' => 'manageOrganization', ':oid' => '[0-9]+'],
            '/colleges/:oid/settings'       => ['callback' => 'manageOrganization', ':oid' => '[0-9]+'],
            '/colleges/:oid/users'          => ['callback' => 'manageUsers', ':oid' => '[0-9]+'],
            '/colleges/:oid/users/:uid'     => ['callback' => 'editUser', ':oid' => '[0-9]+'],
            // '/colleges/:oid/templates/:tid' => ['callback' => 'editTemplate', ':oid' => '[0-9]+'],
            '/colleges/:oid/syllabus/start' => ['callback' => 'start', ':oid' => '[0-9]+'],
            '/colleges/:oid/syllabus/:id'   => ['callback' => 'edit', ':oid' => '[0-9]+|new'],
            '/colleges/:oid/syllabus/:id/view' => ['callback' => 'view', ':oid' => '[0-9]+'],
            '/colleges/:oid/syllabus/:id/delete' => ['callback' => 'delete', ':oid' => '[0-9]+'],
            '/colleges/:oid/templates'      => ['callback' => 'listTemplates', ':oid' => '[0-9]+'],
            '/colleges/:oid/syllabus/startwith/:id'   => ['callback' => 'startWith', ':oid' => '[0-9]+|new'],
        ];
    }

    public function getOrganization ($id=null)
    {
        return $this->getOrganizationSchema()->get($id) ?? $this->getOrganizationSchema()->createInstance();
    }

    public function getOrganizationSchema () { return $this->schema($this->getSchemaName()); }
    
    public function getSchemaName () { return 'Syllabus_AcademicOrganizations_College'; }

}