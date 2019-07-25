<?php

/**
 * Course section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Courses_Course extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_courses',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'title' => 'string',
            'description' => 'string',
            'sectionNumber' => ['string', 'nativeName' => 'section_number'],
            'classNumber' => ['string', 'nativeName' => 'class_number'],
            'semester' => 'string',
            'year' => 'string',
            'externalKey' => ['string', 'nativeName' => 'external_key'],

            'classDataCourseSection' => ['1:1', 'to' => 'Syllabus_ClassData_CourseSection', 'keyMap' => ['external_key' => 'id']],
        ];
    }

    public function processEdit ($request, $data=null) 
    {
        $data = $data ?? $request->getPostParameters();
        if (isset($data['syllabusVersion']) && isset($data['syllabusVersion']['id']) && 
            isset($data['section']) && isset($data['section']['real']))
        {
            if ($externalKey = $data['section']['real']['external_key'])
            {
                $syllabusVersion = $this->getSchema('Syllabus_Syllabus_SyllabusVersion')->get($data['syllabusVersion']['id']);
                $courses = $this->getSchema('Syllabus_ClassData_CourseSection');
                $course = $courses->findOne($courses->id->equals($externalKey));
                $course->syllabus_id = $syllabusVersion->syllabus->id ?? '';
                $course->save();
                $courseData = $course->getData();
                $courseData['semester'] = Syllabus_Admin_Semester::ConvertToTerm($courseData['semester'], true);
                unset($courseData['id']);
                $this->absorbData($courseData);
                $this->externalKey = $course->id;
            }
        }
        $this->save();
    }
}
