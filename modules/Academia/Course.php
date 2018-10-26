<?php

class Syllabus_Academia_Course extends Bss_ActiveRecord_BaseWithAuthorization //implements Bss_AuthZ_IObjectProxy
{
    public static function SchemaInfo ()
    {
        return array(
            '__type' => 'syllabus_courses',
            '__azidPrefix' => 'at:syllabus:academia/Course/',
            '__pk' => array('id'),
            
            'id' => 'int',
            'fullName' => array('string', 'nativeName' => 'full_name'),
            'shortName' => array('string', 'nativeName' => 'short_name'),
            'department' => array('string'),
            'startDate' => array('datetime', 'nativeName' => 'start_date'),
            'endDate' => array('datetime', 'nativeName' => 'end_date'),
            'active' => 'bool',
            'deleted' => 'bool',
            'externalCourseKey' => array('string', 'nativeName' => 'external_course_key'),
            'description' => 'string',

            'createdDate' => array('datetime', 'nativeName' => 'created_date'),
            'modifiedDate' => array('datetime', 'nativeName' => 'modified_date'),

            'departments' => array('1:N', 'to' => 'Syllabus_Academia_Department', 'reverseOf' => 'course', 'orderBy' => array('created_date')), 

        );
    }

    // TODO: figure out which one to use
    // public function getAuthorizationId () { return "{$this->__azidPrefix}{$this->id}"; }
    public function getAuthorizationId () { return "at:syllabus:academia/Group/{$this->id}"; }

    // TODO: figure out which one to use
    // public function getEntity ()
    public function getConcreteEntity ()
    {
        // TODO: figure out which one to use
        // return $this->getSchema(get_class($this))->get($this->id);
        return $this;
    }

    public function getShortNameAbbrev ()
    {
        $nameArr = explode('-', $this->shortName, 3);
        return $nameArr[0] . '-' . $nameArr[1];
    }


}