<?php

/**
 * One-time view/download link
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_TemporaryLink extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_temporary_links',
            '__pk' => ['id'],
            
            'id' => 'int',
            'token' => 'string',        
            'courseSectionId' => ['string', 'nativeName' => 'course_section_id'],
            'syllabusId' => ['string', 'nativeName' => 'syllabus_id'],
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],

            'syllabus' => ['1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => ['syllabus_id' => 'id']],
            'courseSection' => ['1:1', 'to' => 'Syllabus_ClassData_CourseSection', 'keyMap' => ['course_section_id' => 'id']],
        ];
    }

    public function generate ($courseSection)
    {
        $this->_assign('token', $this->getApplication()->generateSecretCode(10));
        $this->_assign('courseSectionId', $courseSection->id);
        $this->_assign('syllabusId', $courseSection->syllabus ? $courseSection->syllabus->id : null);
        $this->_assign('createdDate', new DateTime);
        $this->save();

        return $this;
    }

    public function getUrl ()
    {
        $app = $this->getApplication();
        return $app->baseUrl('syllabus/'.$this->_fetch('courseSectionId').'/view?temp='.$this->_fetch('token'));
    }
}
