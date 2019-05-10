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
            'sectionNumber' => ['string', 'nativeName' => 'section_number'],
            'classNumber' => ['string', 'nativeName' => 'class_number'],
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
            // TODO: Handle this section and multiple syllabi...
            'syllabus'      => ['1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => ['syllabus_id' => 'id']],
        ];
    }

    public function getSemester ($display=false)
    {
        $sem = $this->_fetch('semester');
        if ($display && (strlen($sem) === 1))
        {
            switch ($sem) {
                case '1':
                    $sem = 'Winter'; break;
                case '3':
                    $sem = 'Spring'; break;
                case '5':
                    $sem = 'Summer'; break;
                case '7':
                    $sem = 'Fall'; break;
                default:
            }
        }
        return $sem;
    }

    public function getShortName ()
    {
        $cn = $this->_fetch('classNumber');
        $section = $this->_fetch('sectionNumber');
        // return $cn . " - Section $section";
        return $cn . ".$section";
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
