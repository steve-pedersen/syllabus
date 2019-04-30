<?php

/**
 * Base class for extensions to the Syllabus_Syllabus_Section active record to implement.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
abstract class Syllabus_Syllabus_SectionExtension extends Bss_Core_NamedExtension implements Bss_ActiveRecord_IExtension
{
    public static function getExtensionPointName () { return 'at:syllabus:syllabus/sectionExtensions'; }
    
    abstract public function getExtensionKey ();
    abstract public function getDisplayName ($plural = false);
    abstract public function getHelpText ();
    abstract public function getRecordClass ();
    abstract public function getSectionTasks ();
    abstract public function getEditFormFragment ();
    abstract public function getViewFragment ();

    public function getSchema ()
    {
        if ($recordClass = $this->getRecordClass())
        {
            return $this->getApplication()->schemaManager->getSchema($recordClass);
        }
    }
    
    public function initializeRecord (Bss_ActiveRecord_Base $record) {}

}