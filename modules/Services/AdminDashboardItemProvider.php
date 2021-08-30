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
            'Services' => array('order' => 3),
        );
    }
    
    public function getItems (Bss_Master_UserContext $userContext)
    {
        return array(
            // 'services-screenshotter' => array(
            //     'section' => 'Services',
            //     'order' => 1,
            //     'text' => '<a href="admin/services">Screenshotter API Settings</a>',
            //     'allowHtml' => true,
            // ),
            'at-office' => [
                'section' => 'Services',
                'order' => 3,
                'href' => 'admin/atoffice',
                'text' => 'AT Office API',
            ],
        );
    }
}
