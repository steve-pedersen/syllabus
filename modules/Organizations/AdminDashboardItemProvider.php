<?php

/**
 */
class Syllabus_Organizations_AdminDashboardItemProvider extends At_Admin_DashboardItemProvider
{
    public function getSections (Bss_Master_UserContext $userContext)
    {
        return [
            // 'Organizations' => [
            //     'order' => 2,
            // ],
        ];
    }
    
    public function getItems (Bss_Master_UserContext $userContext)
    {
        return [
            // 'organizations-user-import' => [
            //     'section' => 'Organizations',
            //     'order' => 1,
            //     'href' => 'organizations/admin/import',
            //     'text' => 'Run Organization-Users Import',
            // ],        
        ];
    }
}
