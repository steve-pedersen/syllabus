<?php

/**
 * Enrollment ActiveRecord schema of ClassData/SIS course section data.
 * This table serves as the mapping between Users and CourseSections
 *
 * @author Steve Pedersen (pedersen@sfsu.edu)
 */
class Syllabus_ClassData_Enrollment extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_classdata_enrollments',
            '__pk' => ['userId', 'courseSectionId'],
            
            'userId' => ['string', 'nativeName' => 'user_id'],
            'courseSectionId' => ['string', 'nativeName' => 'course_section_id'],
            'yearSemester' => ['string', 'nativeName' => 'year_semester'],     
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],
            'deleted' => 'bool',
            'role' => 'string',
            
            // 'users' => ['1:N', , 'to' => 'Syllabus_ClassData', 'reverseOf' => '', 'orderBy' => ['lastName']],
        ];
    }
}