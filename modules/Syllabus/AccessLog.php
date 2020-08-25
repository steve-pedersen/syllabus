<?php

/**
 * Syllabus AccessLogs
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_AccessLog extends Bss_ActiveRecord_BaseWithAuthorization 
{
    private $expiration;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_access_logs',
            '__pk' => ['id'],
            
            'id' => 'int',   
            'accessDate' => ['datetime', 'nativeName' => 'access_date'],
            'courseSectionId' => ['string', 'nativeName' => 'course_section_id'],
            'syllabusId' => ['int', 'nativeName' => 'syllabus_id'],
            'accountId' => ['int', 'nativeName' => 'account_id'],

            'courseSection' => ['1:1', 'to' => 'Syllabus_ClassData_CourseSection', 'keyMap' => ['course_section_id' => 'id']],
            'syllabus' => ['1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => ['syllabus_id' => 'id']],
            'user' => ['1:1', 'to' => 'Bss_AuthN_Account', 'keyMap' => ['account_id' => 'id']],
        ];
    }
}
