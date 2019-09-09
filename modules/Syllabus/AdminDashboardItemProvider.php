<?php

/**
 */
class Syllabus_Syllabus_AdminDashboardItemProvider extends At_Admin_DashboardItemProvider
{
    public function getSections (Bss_Master_UserContext $userContext)
    {
        return [
            'Site Settings' => [
                'order' => 2,
            ],
        ];
    }
    
    public function getItems (Bss_Master_UserContext $userContext)
    {
        return [
            'university-templates' => [
                'section' => 'Site Settings',
                'order' => 0,
                'href' => 'admin/templates/university',
                'text' => 'Manage University Templates',
            ],
            'campus-resources' => [
                'section' => 'Site Settings',
                'order' => 2,
                'href' => 'admin/syllabus/resources',
                'text' => 'Manage Campus Resources',
            ],
            'guide-docs' => [
                'section' => 'Site Settings',
                'order' => 3,
                'href' => 'admin/syllabus/guidedocs',
                'text' => 'Shared Resources, Guidelines, and Documents',
            ],
        ];
    }
}
