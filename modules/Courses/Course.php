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

    public function processEdit ($request) 
    {
        $data = $request->getPostParameters();
        if (isset($data['section']) && isset($data['section']['real']))
        {
            $this->absorbData($data['section']['real']);
            $this->externalKey = $data['section']['real']['external_key'];
        }
    }
}
