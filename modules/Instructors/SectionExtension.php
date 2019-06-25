<?php

class Syllabus_Instructors_SectionExtension extends Syllabus_Syllabus_SectionExtension
{
    public static function getExtensionName () { return 'instructors'; }
    
    public function getExtensionKey () { return 'instructor_id'; }
    public function getDisplayName ($plural = false) { return 'Instructors'; }
    public function getHelpText () { return 'Add multiple instructors at a time in this Instructors section type.'; }
    public function getRecordClass () { return 'Syllabus_Instructors_Instructors'; }
    public function getSectionTasks () { return []; }
    public function canHaveMultiple () { return false; }
    public function getEditFormFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_instructors.edit.html.tpl');
    }
    public function getViewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_instructors.view.html.tpl');
    }
    public function getOutputFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_instructors.output.html.tpl');
    }
    public function getExtensionProperties ()
    {
        return [
            'instructors' => ['1:1', 'to' => 'Syllabus_Instructors_Instructors', 'keyMap' => ['instructor_id' => 'id']],
        ];
    }
    
    public function getExtensionMethods() { return []; }

}