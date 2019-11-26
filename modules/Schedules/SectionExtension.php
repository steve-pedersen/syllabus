<?php

class Syllabus_Schedules_SectionExtension extends Syllabus_Syllabus_SectionExtension
{
    public static function getExtensionName () { return 'schedules'; }
    
    public function getExtensionKey () { return 'schedule_id'; }
    public function getDisplayName ($plural = false) { return 'Schedule' . ($plural ? 's' : ''); }
    public function getHelpText () { return ''; }
    public function getRecordClass () { return 'Syllabus_Schedules_Schedules'; }
    public function getSectionTasks () { return []; }
    public function hasDefaults () { return true; }
    public function hasImportableContent () { return true; }
    public function getEditFormFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_schedules.edit.html.tpl');
    }
    public function getViewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_schedules.view.html.tpl');
    }
    public function getOutputFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_schedules.output.html.tpl');
    }
    public function getExportFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_schedules.export.html.tpl');
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
            'schedules' => ['1:1', 'to' => 'Syllabus_Schedules_Schedules', 'keyMap' => ['schedule_id' => 'id']],
        ];
    }
    
    public function getExtensionMethods() { return []; }

}