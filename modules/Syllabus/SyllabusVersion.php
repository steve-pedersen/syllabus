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
        return [
            '__type' => 'syllabus_syllabus_versions',
            '__pk' => ['id'],
            
            'id' => 'int',
            'title' => 'string',
            'description' => 'string', 
            'syllabusId' => ['int', 'nativeName' => 'syllabus_id'],
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
           
            'syllabus' => ['1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => ['syllabus_id' => 'id']],

            'sectionVersions' => ['N:M',
                'to' => 'Syllabus_Syllabus_SectionVersion',
                'via' => 'syllabus_syllabus_version_section_map',
                'fromPrefix' => 'syllabus_version',
                'toPrefix' => 'section_version',
                'properties' => ['sort_order' => 'int', 'read_only' => 'bool', 'is_anchored' => 'bool', 'log' => 'string'],
                'orderBy' => ['+_map.sort_order']
            ],
        ];
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
            $section->log           = $this->sectionVersions->getProperty($sv, 'log');
            $a                      = $this->sectionVersions->getProperty($sv, 'is_anchored');
            $section->isAnchored    = ($a===null || $a===true || $a==='true') ? true : false;
            $sections[] = $section;
        }

        return $sections;
    }

    public function getSectionVersionsWithExt ($withExt=true, $normalizeVersions=true)
    {
        $sectionVersions = [];
        if ($this->sectionVersions)
        {
            foreach ($this->sectionVersions as $sv)
            {
                if ($withExt)
                {
                    $sv->extension = $sv->getExtensionByName(get_class($sv->resolveSection()));
                }
                $sv->sortOrder     = $this->sectionVersions->getProperty($sv, 'sort_order');
                $sv->readOnly      = $this->sectionVersions->getProperty($sv, 'read_only');
                $sv->log           = $this->sectionVersions->getProperty($sv, 'log');
                $a                 = $this->sectionVersions->getProperty($sv, 'is_anchored');
                $sv->isAnchored    = ($a===null || $a===true || $a==='true') ? true : false;
                $sv->normalizedVersion = $sv->section->getNormalizedVersion($sv->id);
                $sectionVersions[] = $sv;
                $counter++;
            }
        }

        return $sectionVersions;
    }
    
    public function getNormalizedVersion ()
    {
        return $this->syllabus->getNormalizedVersion($this->id);
    }

    public function getSectionCount ()
    {
        return count($this->sectionVersions);
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

        return $this->getSchema('Syllabus_Syllabus_SectionVersion')->getExtensions();
    }

    public function createDerivative ()
    {
        $inst = $this->getSchema()->createInstance();

        $inst->_assign('title', $this->title);
        $inst->_assign('description', $this->description);
        $inst->_assign('syllabus_id', $this->syllabus_id);
        $inst->_assign('createdDate', new DateTime);

        $properties = ['sort_order', 'read_only', 'is_anchored', 'log'];

        // Copy sectionVersions fields
        foreach ($this->sectionVersions as $sectionVersion)
        {
            $inst->sectionVersions->add($sectionVersion);
            foreach ($properties as $property)
            {
                $inst->sectionVersions->setProperty($sectionVersion, $property, 
                    $this->sectionVersions->getProperty($sectionVersion, $property)
                );
            }
        }

        return $inst;
    }
}