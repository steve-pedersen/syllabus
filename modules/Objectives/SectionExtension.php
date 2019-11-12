<?php

class Syllabus_Objectives_SectionExtension extends Syllabus_Syllabus_SectionExtension
{
    public static function getExtensionName () { return 'objectives'; }
    
    public function getExtensionKey () { return 'objectives_id'; }
    public function getDisplayName ($plural = false) { return 'Objectives'; }
    public function getHelpText () { return 'Objectives section type of a syllabus.'; }
    public function getRecordClass () { return 'Syllabus_Objectives_Objectives'; }
    public function getSectionTasks () { return []; }
    public function getEditFormFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_objectives.edit.html.tpl');
    }
    public function getViewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_objectives.view.html.tpl');
    }
    public function getOutputFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_objectives.output.html.tpl');
    }
    public function getExportFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_objectives.export.html.tpl');
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
            'objectives' => ['1:1', 'to' => 'Syllabus_Objectives_Objectives', 'keyMap' => ['objectives_id' => 'id']],
        ];
    }
    
    public function getExtensionMethods() { return []; }

}