<?php

/**
 * Versioning table for Syllabus active records.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_SyllabusVersion extends Bss_ActiveRecord_Base
{
    private $_sections;
    private $_sectionVersions;
    private $_sectionExtensions;

    public static function SchemaInfo ()
    {
        return array(
            '__type' => 'syllabus_syllabus_versions',
            '__pk' => array('id'),
            
            'id' => 'int',
            'title' => 'string',
            'description' => 'string', 
            'syllabusId' => array('int', 'nativeName' => 'syllabus_id'),
            'createdDate' => array('datetime', 'nativeName' => 'created_date'),
           
            'syllabus' => array('1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => array('syllabus_id' => 'id')),

            'sectionVersions' => array('N:M',
                'to' => 'Syllabus_Syllabus_SectionVersion',
                'via' => 'syllabus_syllabus_version_section_map',
                'fromPrefix' => 'syllabus_version',
                'toPrefix' => 'section_version',
                'properties' => array('sort_order' => 'int', 'read_only' => 'bool', 'is_anchored' => 'bool', 'log' => 'string'),
                'orderBy' => array('+_map.sort_order')
            ),
        );
    }

    /**
     * Returns an array of Section objects with the following additional useful properties.
     *
     *  - SectionExtension ($section->extension) for resolved, real section can be accessed:
     *      if ($withExt == true)
     *
     *  - SectionVersion ($section->version) for this syllabus version can always be accessed
     *
     *  - N:M property access from $this->sectionVersions, via the $section object directly
     *      $section->sortOrder
     *      $section->readOnly
     *      $section->isAnchored
     *      $section->log
     */    
    public function getSections ($withExt=false)
    {
        $sections = [];
        foreach ($this->sectionVersions as $sv)
        {
            $section = $sv->section;
            $section->version = $sv;
            if ($withExt)
            {
                $section->extension = $sv->getExtensionByName(get_class($sv->resolveSection()));
            }
            $section->sortOrder     = $this->sectionVersions->getProperty($sv, 'sort_order');
            $section->readOnly      = $this->sectionVersions->getProperty($sv, 'read_only');
            $section->isAnchored    = $this->sectionVersions->getProperty($sv, 'is_anchored');
            $section->log           = $this->sectionVersions->getProperty($sv, 'log');
            $sections[] = $section;
        }

        return $sections;
    }

    /**
     * Returns all SectionExtensions
     */    
    public function getSectionExtensions ()
    {
        if (empty($this->_sectionExtensions))
        {
            $this->_sectionExtensions = $this->getSchema('Syllabus_Syllabus_SectionVersion')->getExtensions();
        }

        return $this->_sectionExtensions;
    }
}