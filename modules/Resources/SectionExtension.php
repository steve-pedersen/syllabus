<?php

class Syllabus_Resources_SectionExtension extends Syllabus_Syllabus_SectionExtension
{
    public static function getExtensionName () { return 'resources'; }
    
    public function getExtensionKey () { return 'resources_id'; }
    public function getDisplayName ($plural = false) { return 'Resources'; }
    public function getHelpText () { return 'You may choose from preset Campus Resources or create your own custom ones.'; }
    public function getRecordClass () { return 'Syllabus_Resources_Resources'; }
    public function getSectionTasks () { return []; }
    public function getEditFormFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_resources.edit.html.tpl');
    }
    public function getViewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_resources.view.html.tpl');
    }
    public function getOutputFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_resources.output.html.tpl');
    }
    public function getExtensionProperties ()
    {
        return [
            'resources' => ['1:1', 'to' => 'Syllabus_Resources_Resources', 'keyMap' => ['resources_id' => 'id']],
        ];
    }
    
    public function getExtensionMethods() { return []; }

}