<?php

/**
 */
class Syllabus_Organizations_GroupController extends Syllabus_Organizations_BaseController
{
    public static function getRouteMap ()
    {
        return [

        ];
    }

	public function getOrganization ()
	{
		return $this->schema('Syllabus_Organizations_Group');
	}
}