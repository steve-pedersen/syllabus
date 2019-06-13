<?php

class Syllabus_Containers_SectionExtension extends Syllabus_Syllabus_SectionExtension
{
    public static function getExtensionName () { return 'containers'; }
    
    public function getExtensionKey () { return 'containers_id'; }
    public function getDisplayName ($plural = false) { return 'Container'; }
    public function getHelpText () { return 'Container is a section type which allows you to group other sections together (e.g. Policy Container for all of your policies).'; }
    public function getRecordClass () { return 'Syllabus_Containers_Container'; }
    public function getSectionTasks () { return []; }
    public function getEditFormFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_container.edit.html.tpl');
    }
    public function getViewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_container.view.html.tpl');
    }
    public function getExtensionProperties ()
    {
        return [
            'containers' => ['1:1', 'to' => 'Syllabus_Containers_Container', 'keyMap' => ['containers_id' => 'id']],
        ];
    }
    
    public function getExtensionMethods() { return []; }

}