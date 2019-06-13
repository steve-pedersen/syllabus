<?php

/**
 */
class Syllabus_Syllabus_AdminDashboardItemProvider extends At_Admin_DashboardItemProvider
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
            'language-strings' => array(
                'section' => 'Site Settings',
                'order' => 1,
                'href' => 'admin/syllabus/resources',
                'text' => 'Configure campus resources',
            ),
        );
    }
}
