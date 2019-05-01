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

	public function getOrganization ($id=null)
	{
		$schema = $this->schema('Syllabus_AcademicOrganizations_College');
		return $schema->get($id) ?? $schema->createInstance();
	}
}