<?php

class Syllabus_Policies_SectionExtension extends Syllabus_Syllabus_SectionExtension
{
    public static function getExtensionName () { return 'policies'; }
    
    public function getExtensionKey () { return 'policies_id'; }
    public function getDisplayName ($plural = false) { return 'Policies'; }
    public function getHelpText () { return ''; }
    public function getRecordClass () { return 'Syllabus_Policies_Policies'; }
    public function getSectionTasks () { return []; }
    public function getSectionOrder () { return 8; }
    public function hasImportableContent () { return true; }
    public function getEditFormFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_policies.edit.html.tpl');
    }
    public function getViewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_policies.view.html.tpl');
    }
    public function getOutputFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_policies.output.html.tpl');
    }
    public function getExportFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_policies.export.html.tpl');
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
            'policies' => ['1:1', 'to' => 'Syllabus_Policies_Policies', 'keyMap' => ['policies_id' => 'id']],
        ];
    }
    
    public function getExtensionMethods() { return []; }

}