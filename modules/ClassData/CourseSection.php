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
                'orderBy' => ['-_map.role', 'lastName', 'firstName'],
            ],

            'course'        => ['1:1', 'to' => 'Syllabus_ClassData_Course', 'keyMap' => ['course_id' => 'id']],
            'department'    => ['1:1', 'to' => 'Syllabus_AcademicOrganizations_Department', 'keyMap' => ['department_id' => 'id']],
            // TODO: Handle this section and multiple syllabi...
            'syllabus'      => ['1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => ['syllabus_id' => 'id']],
        ];
    }

    public function getSubmission ()
    {
        $schema = $this->getSchema('Syllabus_Syllabus_Submission');
        $condition = $schema->allTrue(
            $schema->course_section_id->equals($this->id),
            $schema->deleted->isNull()->orIf($schema->deleted->isFalse())
        );

        return $schema->findOne($condition, ['orderBy' => 'modifiedDate']);
    }

    public function getTerm ($internal=false)
    {
        $term = $this->getSemester(true) . ' ' . $this->_fetch('year');
        if ($internal)
        {
            $term = self::ConvertToCode($term);
        }

        return $term;
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

    public function getShortName ($full=false)
    {
        $cn = $this->_fetch('classNumber');
        $section = $this->_fetch('sectionNumber');
        if ($full)
        {
            return $cn . "-$section " . $this->getTerm();
        }

        return $cn . "-$section";
    }

    public function getFullDisplayName ()
    {
        return $this->shortName.' - '.$this->_fetch('title');
    }

    public function getFullSummary ()
    {
        return $this->getFullDisplayName() .' ['.$this->getTerm().']';
    }

    public function isTaughtByUser ($user)
    {
        $isTaughtByUser = false;
        foreach ($this->enrollments as $enrollment)
        {
            if ($enrollment->id === $user->username &&
                $this->enrollments->getProperty($enrollment, 'role') === 'instructor')
            {
                $isTaughtByUser = true;
                break;
            }
        }

        return $isTaughtByUser;
    }

    public function getRelevantPastCoursesWithSyllabi ($user, $limit=-1)
    {
        $pastCourseSections = [];
        $syllabusIds = [];

        // step-1
        // find all $this->course->courseSections that also have ->syllabus
        // limit(step-1) = limit

        foreach ($user->classDataUser->enrollments as $courseSection)
        {
            // ensure same course but not same course section as the one that is calling this function.
            if ($this->course && ($this->course->id === @$courseSection->course->id) && ($this->id !== $courseSection->id))
            {
                if ($courseSection->syllabus)
                {
                    $pastCourseSections[$courseSection->id] = $courseSection;
                    $syllabusIds[] = $courseSection->syllabus->id;
                }
            }
            if (count($pastCourseSections) === $limit) break;
        }

        // step-2
        // search for other courseSections that have same/similar classNumber and title,
        // as well as have ->syllabus. check that they aren't already added to $pastCourseSections.
        // if count(step-1) == limit, then check count(step-2) and replace 2nd half of step-1 
        // elements with at most limit/2 step-2 elements

        // step-3
        // add a few syllabi that don't have course information sections, aka detached syllabi
        $schema = $this->getSchema('Syllabus_Syllabus_Syllabus');
        $results = $schema->find(
            $schema->createdById->equals($user->id)->andIf($schema->id->notInList($syllabusIds)),
            ['orderBy' => ['+modifiedDate', '+createdDate']]
        );
        $counter = 0;
        $max = 3;
        foreach ($results as $syllabus)
        {
            if ($syllabus->latestVersion && !$syllabus->latestVersion->courseInfoSection)
            {
                $pastCourseSections[$syllabus->id] = $syllabus;
                $counter++;
            }
            if ($counter === $max) break;
        }

        return $pastCourseSections;
    }

    public static function ConvertToCode ($display)
    {
        $space = strpos($display, ' ');
        $term = substr($display, 0, $space);
        $year = substr($display, $space + 1);

        switch ($term) {
            case 'Winter':
                $term = 1;
                break;
            
            case 'Spring':
                $term = 3;
                break;

            case 'Summer':
                $term = 5;
                break;

            case 'Fall':
                $term = 7;
                break;
        }

        return $year[0] . $year[2] . $year[3] . $term;
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
