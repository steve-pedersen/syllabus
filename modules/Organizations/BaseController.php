<?php

/**
 */
abstract class Syllabus_Organizations_BaseController extends Syllabus_Master_Controller
{
    public static function getRouteMap ()
    {
        return [

        ];
    }

	abstract public function getOrganization ();

	// protected function afterCallback ($callback)
	// {
	// 	parent::afterCallback($callback);

	// }

    public function listOrganizations ()
    {

    	$this->template->organization = $this->getOrganization();
    }

    public function dashboard ()
    {

    }

    public function manageOrganization ()
    {

    }

    public function manageUsers ()
    {

    }
}