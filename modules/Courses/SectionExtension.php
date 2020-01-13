<?php

class Syllabus_Courses_SectionExtension extends Syllabus_Syllabus_SectionExtension
{
    public static function getExtensionName () { return 'course'; }
    
    public function getExtensionKey () { return 'course_id'; }
    public function getDisplayName ($plural = false) { return 'Course Information'; }
    public function getHelpText () { return ''; }
    public function getRecordClass () { return 'Syllabus_Courses_Course'; }
    public function getSectionTasks () { return []; }
    public function getSectionOrder () { return 0; }
    public function canHaveMultiple () { return false; }
    public function getEditFormFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_course.edit.html.tpl');
    }
    public function getViewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_course.view.html.tpl');
    }
    public function getOutputFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_course.output.html.tpl');
    }
    public function getAddonFormFragment () 
    { 
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_lookup.html.tpl');
    }
    public function getExportFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_course.export.html.tpl');
    }
    public function getExtensionProperties ()
    {
        return [
            'course' => ['1:1', 'to' => 'Syllabus_Courses_Course', 'keyMap' => ['course_id' => 'id']],
        ];
    }
    
    public function getExtensionMethods() { return []; }

}