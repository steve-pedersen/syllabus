<?php

class Syllabus_LearningOutcomes_SectionExtension extends Syllabus_Syllabus_SectionExtension
{
    public static function getExtensionName () { return 'learning_outcomes'; }
    
    public function getExtensionKey () { return 'learning_outcomes_id'; }
    public function getDisplayName ($plural = false) { return 'Student Learning Outcomes'; }
    public function getHelpText () { return "The bullet list format will display each SLO on it's own bullet point. The 2 or 3 column table formats will display each SLO in the first column of each row."; }
    public function getRecordClass () { return 'Syllabus_LearningOutcomes_LearningOutcomes'; }
    public function getSectionTasks () { return []; }
    public function getSectionOrder () { return 10; }
    public function canHaveMultiple () { return false; }
    public function hasDefaults () { return false; }
    public function hasImportableContent () { return false; }
    public function getEditFormFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_learningOutcomes.edit.html.tpl');
    }
    public function getViewFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_learningOutcomes.view.html.tpl');
    }
    public function getOutputFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_learningOutcomes.output.html.tpl');
    }
    public function getExportFragment ()
    {
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_learningOutcomes.export.html.tpl');
    }
    public function getAddonFormFragment () 
    { 
        return Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', '_lookup.html.tpl');
    }
    public function getExtensionProperties ()
    {
        return [
            'learningOutcomes' => ['1:1', 'to' => 'Syllabus_LearningOutcomes_LearningOutcomes', 'keyMap' => ['learning_outcomes_id' => 'id']],
        ];
    }
    
    public function getExtensionMethods() { return []; }

}