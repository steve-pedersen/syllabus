<?php

/**
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Welcome_AdminDashboardItemProvider extends At_Admin_DashboardItemProvider
{
    public function getSections (Bss_Master_UserContext $userContext)
    {
        return array(
            // 'Welcome' => array('order' => 3),
        );
    }
    
    public function getItems (Bss_Master_UserContext $userContext)
    {
        return array(
            // 'welcome-text' => array(
            //     'section' => 'Welcome',
            //     'order' => 1,
            //     'text' => 'Change Welcome page text',
            //     'href' => 'admin/welcome',
            // ),
        );
    }
}
