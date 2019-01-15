<?php

/**
 * Parent page for admin controllers of syllabus. This is mainly for flash messages.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Master_AdminController extends At_Admin_Controller
{
    // NOTE: apache logs complaining to put this func in here, so here it is...
    public static function getRouteMap ()
    {
        return array();
    }

    protected function flash ($content, $class='success') {
        $session = $this->request->getSession();
        $session->flashContent = $content;
        $session->flashClass = $class;
    }

    protected function afterCallback ($callback)
    {
        $session = $this->request->getSession();
        if (isset($session->flashContent))
        {
            $this->template->flashContent = $session->flashContent;
            unset($session->flashContent);
        }
        if (isset($session->flashClass))
        {
            $this->template->flashClass = $session->flashClass;
            unset($session->flashClass);
        }
        parent::afterCallback($callback);
    }
}