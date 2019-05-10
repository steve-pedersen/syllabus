<?php

/**
 * Extension point for extensions to the Syllabus_ record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_SectionExtensionPoint extends Bss_Core_ExtensionPoint
{
    public function getUnqualifiedName () { return 'sectionExtensions'; }
    public function getDescription () { return 'Extensions add properties and methods to the Syllabus_Syllabus_Section active record.'; }
    public function getRequiredInterfaces () { return ['Syllabus_Syllabus_SectionExtension']; }
}
