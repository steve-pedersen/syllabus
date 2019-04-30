<?php

/**
 */
class Syllabus_AcademicOrganizations_CollegeController extends Syllabus_Organizations_BaseController
{
    public static function getRouteMap ()
    {
        return [

        ];
    }

	public function getOrganization ()
	{
		return $this->schema('Syllabus_AcademicOrganizations_College');
	}
}