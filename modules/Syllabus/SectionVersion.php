<?php

/**
 * Versioning table for Syllabus Section active records.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_SectionVersion extends Bss_ActiveRecord_Base
{
    private $_realSection;
    private $_sectionExtension;

    public static function SchemaInfo ()
    {
        return array(
            '__type' => 'syllabus_section_versions',
            '__pk' => array('id'),
            '__azidPrefix' => 'at:syllabus:syllabus/Section/',
            '__extensionPoint' => 'at:syllabus:syllabus/sectionExtensions',
            
            'id' => 'int',
            'title' => 'string',
            'description' => 'string', 
            'sectionId' => array('int', 'nativeName' => 'section_id'),
            'createdDate' => array('datetime', 'nativeName' => 'created_date'),
           
            'section' => array('1:1', 'to' => 'Syllabus_Syllabus_Section', 'keyMap' => array('section_id' => 'id')),
            'subsections' => array('1:N', 'to' => '', 'reverseOf' => 'parent', 'orderBy' => array('+sortOrder')),

            // probably don't need this
            'syllabusVersions' => array('N:M',
                'to' => 'Syllabus_Syllabus_SyllabusVersion',
                'via' => 'syllabus_syllabus_version_section_map',
                'fromPrefix' => 'section_version',
                'toPrefix' => 'syllabus_version',
                'properties' => array('sort_order' => 'int', 'read_only' => 'bool', 'is_anchored' => 'bool', 'log' => 'string'),
                'orderBy' => array('+_map.sort_order')
            ),

        );
    }

    public function resolveSection ()
    {
        $extensions = $this->_schema->getExtensions();
        foreach ($extensions as $ext)
        {
            $key = $ext->getExtensionKey();
            if (isset($this->$key) && ($this->$key !== 0))
            {
                $this->_realSection = $this->getSchema($ext->getRecordClass())->get($this->$key);
            }
        }

        return $this->_realSection ?? null;
    }

    public function getExtensionByName ($record)
    {
        if ($this->_sectionExtension === null)
        {
            $moduleManager = $this->getApplication()->moduleManager;
            $extensions = $this->_schema->getExtensions();
            foreach ($extensions as $ext)
            {
                if ($ext->getRecordClass() === $record)
                {
                    $this->_sectionExtension = $moduleManager->getExtensionByName($ext::getExtensionPointName(), $ext::getExtensionName());
                }
            }
        }

        return $this->_sectionExtension;
    }
}