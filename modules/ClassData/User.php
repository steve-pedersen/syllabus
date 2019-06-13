<?php

/**
 * User ActiveRecord schema of ClassData/SIS course section data.
 *
 * @author Steve Pedersen (pedersen@sfsu.edu)
 */
class Syllabus_ClassData_User extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_classdata_users',
            '__pk' => ['id'],
            
            'id' => 'string',
            'firstName' => ['string', 'nativeName' => 'first_name'],
            'lastName' => ['string', 'nativeName' => 'last_name'],
            'emailAddress' => ['string', 'nativeName' => 'email_address'],
                 
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],
            'deleted' => 'bool',
            
            'enrollments' => ['N:M',
                'to' => 'Syllabus_ClassData_CourseSection',
                'via' => 'syllabus_classdata_enrollments',
                'fromPrefix' => 'user',
                'toPrefix' => 'course_section',
                'properties' => [
                    'year_semester' => 'string', 
                    'role' => 'string', 
                    'deleted' => 'bool',
                    'created_date' => 'datetime',
                    'modified_date' => 'datetime',
                ],
                'orderBy' => ['-_map.year_semester', 'classNumber', 'sectionNumber'],
            ],
        ];
    }

    public function getCurrentEnrollments ()
    {
        $y = date('Y');
        $y = '2018';
        $current = [];
        foreach ($this->enrollments as $courseSection)
        {

            if ($courseSection->year >= $y)
            {
                $current[] = $courseSection;
            }
        }

        return $current;
    }
}