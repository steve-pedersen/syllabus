<?php

/**
 */
class Syllabus_ClassData_AdminDashboardItemProvider extends At_Admin_DashboardItemProvider
{
    public function getSections (Bss_Master_UserContext $userContext)
    {
        return array(
            // 'ClassData' => array(
            //     'order' => 1,
            // ),
        );
    }
    
    public function getItems (Bss_Master_UserContext $userContext)
    {
        return array(
            'classdata-set-api' => array(
                'section' => 'Services',
                'order' => 0,
                'href' => 'admin/classdata',
                'text' => 'Set ClassData API values',
            ),
            'cs-import' => array(
                'section' => 'Services',
                'order' => 1,
                'href' => 'admin/classdata/import',
                'text' => 'Run ClassData Import',
            ),
            
        );
    }
}
