<?php

class Syllabus_Grades_SectionExtension extends Syllabus_Syllabus_SectionExtension
{
    public static function getExtensionName () { return 'grades'; }
    
    public function getExtensionKey () { return 'grading_id'; }
    public function getDisplayName ($plural = false) { return 'Grades'; }
    public function getHelpText () { return 'The Grades section type is for letter grade tables and grade breakdowns. It is suggested to add a new Grades section for each new table.'; }
    public function getRecordClass () { return 'Syllabus_Grades_Grades'; }
    public function getSectionTasks () { return []; }
    public function canHaveMultiple () { return true; }
    public function hasDefaults () { return true; }
    public function getEditFormFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_grades.edit.html.tpl');
    }
    public function getViewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_grades.view.html.tpl');
    }
    public function getOutputFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_grades.output.html.tpl');
    }
    public function getExportFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_grades.export.html.tpl');
    }
    public function getImportFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_import.html.tpl');
    }
    public function getPreviewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_preview.html.tpl');
    }
    public function getExtensionProperties ()
    {
        return [
            'grades' => ['1:1', 'to' => 'Syllabus_Grades_Grades', 'keyMap' => ['grading_id' => 'id']],
        ];
    }
    
    public function getExtensionMethods() { return []; }

}