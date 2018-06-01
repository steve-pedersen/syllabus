<?php

/**
 * 
 * @author      Daniel A. Koepke (dkoepke@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_AuthN_AdminDashboardItemProvider extends At_Admin_DashboardItemProvider
{
    public function getSections (Bss_Master_UserContext $userContext)
    {
        return array(
            'Authorization' => array('order' => 1),
        );
    }
    
    public function getItems (Bss_Master_UserContext $userContext)
    {
        return array(
            'authn-roles' => array(
                'section' => 'Authorization',
                'order' => 1,
                'text' => '<a href="admin/roles">Manage Roles</a>',
                'allowHtml' => true,
            ),
            'authn-rolesnew' => array(
                'section' => 'Authorization',
                'order' => 2,
                'text' => '<a href="admin/roles/new">Create New Role</a>',
                'allowHtml' => true,
            ),
            'authn-accesslevel' => array(
                'section' => 'Authorization',
                'order' => 3,
                'text' => '<a href="admin/levels/new">Create New Access Level</a>',
                'allowHtml' => true,
            ),
        );
    }
}
