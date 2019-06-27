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
            // 'courseSection' => ['1:1', 'to' => 'Syllabus_ClassData_CourseSection', 'keyMap' => ['course_section_id' => 'id']],

            'sectionVersions' => ['N:M',
                'to' => 'Syllabus_Syllabus_SectionVersion',
                'via' => 'syllabus_syllabus_version_section_version_map',
                'fromPrefix' => 'syllabus_version',
                'toPrefix' => 'section_version',
                'properties' => [
                    'sort_order' => 'int', 'read_only' => 'bool', 'inherited' => 'bool', 'is_anchored' => 'bool', 'log' => 'string'
                ],
                'orderBy' => ['+_map.sort_order']
            ],
        ];
    }

    /**
     * Returns an array of SectionVersion objects with properties used by the editor.
     */   
    public function getSectionVersionsWithExt ($withExt=true, $normalizeVersions=true)
    {
        $sectionVersions = [];
        if ($this->sectionVersions)
        {
            foreach ($this->sectionVersions as $sv)
            {
                $oldSv = $sv;
                if ($this->sectionVersions->getProperty($sv, 'inherited'))
                {
                    $sv = $sv->section->latestVersion;
                    $sv->inherited = true;
                }
                if ($withExt)
                {
                    $sv->extension = $oldSv->getExtensionByRecord(get_class($oldSv->resolveSection()));
                }
                $sv->sortOrder     = $this->sectionVersions->getProperty($oldSv, 'sort_order');
                $sv->readOnly      = $this->sectionVersions->getProperty($oldSv, 'read_only');
                $sv->log           = $this->sectionVersions->getProperty($oldSv, 'log');
                $a                 = $this->sectionVersions->getProperty($oldSv, 'is_anchored');
                $sv->isAnchored    = ($a===null || $a===true || $a==='true') ? true : false;
                $sv->normalizedVersion = $sv->section->getNormalizedVersion($sv->id);
                $sectionVersions[] = $sv;
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

    public function getOrganization ()
    {
        $organization = null;
        foreach ($this->syllabus->versions as $syllabusVersion)
        {
            if ($syllabusVersion->syllabus->templateAuthorizationId)
            {
                list($type, $id) = explode('/', $syllabusVersion->syllabus->templateAuthorizationId);
                switch ($type)
                {
                    case 'departments':
                        $organization = $this->schema('Syllabus_AcademicOrganizations_Department')->get($id);
                        break;
                    case 'colleges':
                        $organization = $this->schema('Syllabus_AcademicOrganizations_College')->get($id);
                        break;
                    default:
                        break;
                }
                break;
            }            
        }

        return $organization;
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

    public function createDerivative ($clone=false)
    {
        $inst = $this->getSchema()->createInstance();

        $inst->_assign('title', $this->title);
        $inst->_assign('description', $this->description);
        $inst->_assign('syllabus_id', $this->syllabus_id);
        $inst->_assign('createdDate', new DateTime);

        $properties = ['sort_order', 'read_only', 'is_anchored', 'log', 'inherited'];

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

            if ($clone)
            {
                $inst->sectionVersions->setProperty($sectionVersion, 'inherited', true);
            }
        }

        return $inst;
    }
}