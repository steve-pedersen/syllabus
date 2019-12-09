<?php

/**
 */
class Syllabus_Admin_AdminDashboardItemProvider extends At_Admin_DashboardItemProvider
{
    public function getSections (Bss_Master_UserContext $userContext)
    {
        return [
            // 'Site Settings' => [
            //     'order' => 2,
            // ],
        ];
    }
    
    public function getItems (Bss_Master_UserContext $userContext)
    {
        return [
            'dates-set-semester' => [
                'section' => 'Site Settings',
                'order' => 1,
                'href' => 'admin/semesters',
                'text' => 'Set active and visible semesters',
            ],
            'email-settings' => [
                'section' => 'Site Settings',
                'order' => 5,
                'href' => 'admin/settings/email',
                'text' => 'Email Settings',
            ],
        ];
    }
}
