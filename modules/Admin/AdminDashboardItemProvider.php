<?php

/**
 */
class Syllabus_Admin_AdminDashboardItemProvider extends At_Admin_DashboardItemProvider
{
    public function getSections (Bss_Master_UserContext $userContext)
    {
        return array(
            'Site Settings' => array(
                'order' => 2,
            ),
        );
    }
    
    public function getItems (Bss_Master_UserContext $userContext)
    {
        return array(
            'dates-set-semester' => array(
                'section' => 'Site Settings',
                'order' => 0,
                'href' => 'admin/semesters',
                'text' => 'Set active and visible semesters',
            ),
            'language-strings' => array(
                'section' => 'Site Settings',
                'order' => 1,
                'href' => 'admin/language',
                'text' => 'Configure language strings',
            ),
        );
    }
}
