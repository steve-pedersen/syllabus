<?php

/**
 * CourseSection ActiveRecord schema of ClassData/SIS course section data.
 *
 * @author Steve Pedersen (pedersen@sfsu.edu)
 */
class Syllabus_ClassData_CourseSection extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_classdata_course_sections',
            '__pk' => ['id'],
            
            'id' => 'string',
            'title' => 'string',           
            'section_number' => 'string',
            'class_number' => 'string',
            'semester' => 'string',
            'year' => 'string',
            'description' => 'string',  
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],
            'deleted' => 'bool',

            'enrollments' => ['N:M',
                'to' => 'Syllabus_ClassData_User',
                'via' => 'syllabus_classdata_enrollments',
                'fromPrefix' => 'course_section',
                'toPrefix' => 'user',
                'properties' => [
                    'year_semester' => 'string', 
                    'role' => 'string', 
                    'deleted' => 'bool',
                    'created_date' => 'datetime',
                    'modified_date' => 'datetime',
                ],
                'orderBy' => ['course_section_id'],
            ],

            'course'        => ['1:1', 'to' => 'Syllabus_ClassData_Course', 'keyMap' => ['course_id' => 'id']],
            'department'    => ['1:1', 'to' => 'Syllabus_AcademicOrganizations_Department', 'keyMap' => ['department_id' => 'id']],
            // 'syllabus'      => ['1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => ['syllabus_id' => 'id']],
        ];
    }


    // public function getInstructors ($reload = false)
    // {
    //     $users = $this->getSchema('Syllabus_ClassData_User');
    //     $courseSym = new Bss_ActiveRecord_RawSymbol('enrollments', 'course__section_id', 'string');
    //     $cond = $courseSym->equals($this->id);
    //     $roleSym = new Bss_ActiveRecord_RawSymbol('enrollments', 'role', 'string');
    //     $cond = $cond->andIf($roleSym->equals('instructor'));

    //     $instructors = $users->find(
    //         $cond,
    //         array(
    //             'arrayKey' => 'id',
    //             'orderBy' => array('+lastName', '+firstName'),
    //             'extraJoins' => array(
    //                 'enrollments' => array(
    //                     'to' => 'syllabus_classdata_enrollments',
    //                     'on' => array('id' => 'user_id'),
    //                     'type' => Bss_DataSource_SelectQuery::INNER_JOIN,
    //                 ),
    //             )
    //         )
    //     );

    //     return $instructors;
    // }
}
