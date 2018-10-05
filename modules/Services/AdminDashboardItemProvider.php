<?php

/**
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Services_AdminDashboardItemProvider extends At_Admin_DashboardItemProvider
{
    public function getSections (Bss_Master_UserContext $userContext)
    {
        return array(
            'Services' => array('order' => 1),
        );
    }
    
    public function getItems (Bss_Master_UserContext $userContext)
    {
        return array(
            'services-screenshotter' => array(
                'section' => 'Services',
                'order' => 1,
                'text' => '<a href="admin/services">Screenshotter API Settings</a>',
                'allowHtml' => true,
            ),
        );
    }
}
