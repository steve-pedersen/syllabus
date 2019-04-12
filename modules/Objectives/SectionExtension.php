<?php

class Syllabus_Objectives_SectionExtension extends Syllabus_Syllabus_SectionExtension
{
    public static function getExtensionName () { return 'objectives'; }
    
    public function getExtensionKey () { return 'objectives_id'; }
    public function getDisplayName ($plural = false) { return 'Objectives'; }
    public function getHelpText () { return 'Objectives section type of a syllabus.'; }
    public function getRecordClass () { return 'Syllabus_Objectives_Objective'; }
    public function getSectionTasks () { return array(); }
    public function getEditFormFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_objectives.edit.html.tpl');
    }
    public function getViewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_objectives.view.html.tpl');
    }
    public function getExtensionProperties ()
    {
        return array(
            'objectives' => array('1:1', 'to' => 'Syllabus_Objectives_Objective', 'keyMap' => array('objectives_id' => 'id')),
        );
    }
    
    public function getExtensionMethods() { return array(); }

}