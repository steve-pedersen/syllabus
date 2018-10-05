<?php

/**
 * Administrative dashboard.
 * 
 * Customize the At_Admin dashboard for use with Bootstrap 4
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edi)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Admin_DashboardController extends At_Admin_DashboardController
{
    public static function getRouteMap ()
    {
        return array(
            '/admin' => array('callback' => 'dashboard'),
        );
    }
    
    /**
     * Displays the administrative dashboard.
     */
    public function dashboard ()
    {
        parent::dashboard();
    }
}
