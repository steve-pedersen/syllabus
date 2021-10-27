<?php

/**
 * Faculty scheduled courses and their physical/virtual meeting location.
 *
 * @author Steve Pedersen (pedersen@sfsu.edu)
 */
class Syllabus_ClassData_CourseSchedule extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_classdata_course_schedules',
            '__pk' => ['id'],
            
            'id' => 'string',
            'termYear' => ['string', 'nativeName' => 'term_year'], 
            'courseType' => ['string', 'nativeName' => 'course_type'], //sync, async, hybrid, etc.
            'userDeleted' => ['bool', 'nativeName' => 'user_deleted'],
            'schedules' => 'string',

            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],

            'faculty' => ['1:1', 'to' => 'Syllabus_ClassData_User', 'keyMap' => ['faculty_id' => 'id']],
            'account' => ['1:1', 'to' => 'Bss_AuthN_Account', 'keyMap' => ['account_id' => 'id']],
            'course' => ['1:1', 'to' => 'Syllabus_ClassData_CourseSection', 'keyMap' => ['course_section_id' => 'id']],
        ];
    }


}
