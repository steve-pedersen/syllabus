<?php

/**
 * TODO: integrate generic course stuff
 */
class Syllabus_ClassData_Course extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_classdata_courses',
            '__pk' => ['id'],
            
            'id' => 'string',  
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],
            'deleted' => 'bool',

            'department'    => ['1:1', 'to' => 'Syllabus_AcademicOrganizations_Department', 'keyMap' => ['department_id' => 'id']],
            'sections' => ['1:N', 'to' => 'Syllabus_ClassData_CourseSection', 'reverseOf' => 'course', 'orderBy' => ['+createdDate']],
        ];
    }

    
    // public function getSections ($term) { }
}