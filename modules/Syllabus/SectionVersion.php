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
        return [
            '__type' => 'syllabus_section_versions',
            '__pk' => ['id'],
            '__azidPrefix' => 'at:syllabus:syllabus/Section/',
            '__extensionPoint' => 'at:syllabus:syllabus/sectionExtensions',
            
            'id' => 'int',
            'title' => 'string',
            'description' => 'string', 
            'sectionId' => ['int', 'nativeName' => 'section_id'],
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
           
            'section' => ['1:1', 'to' => 'Syllabus_Syllabus_Section', 'keyMap' => ['section_id' => 'id']],
            // 'subsections' => array('1:N', 'to' => '', 'reverseOf' => 'parent', 'orderBy' => array('+sortOrder')),

            // 'container' => ['1:1', 'to' => 'Syllabus_Syllabus_SectionVersion', 'keyMap' => ['container_group_id' => 'id']],
            // 'containerItems' => ['1:N', 'to' => 'Syllabus_Syllabus_SectionVersion', 'reverseOf' => 'container', 'orderBy' => ['+sortOrder']],

            // probably don't need this
            'syllabusVersions' => ['N:M',
                'to' => 'Syllabus_Syllabus_SyllabusVersion',
                'via' => 'syllabus_syllabus_version_section_version_map',
                'fromPrefix' => 'section_version',
                'toPrefix' => 'syllabus_version',
                'properties' => [
                    'sort_order' => 'int', 'read_only' => 'bool','inherited' => 'bool', 'is_anchored' => 'bool', 'log' => 'string'
                ],
                'orderBy' => ['+_map.sort_order']
            ],

        ];
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

    public function createDerivative ($version = null)
    {
        $properties = ['sortOrder', 'readOnly', 'isAnchored', 'inherited', 'log', 'id'];
        $deriv = $this->schema->createInstance();
        foreach ($this->getData() as $key => $val)
        {
            if (!in_array($key, $properties))
            {
                $deriv->$key = $val;
            }
        }
        $deriv->createdDate = new DateTime;
        
        return $deriv;
    }

    public function getNormalizedVersion ()
    {
        return $this->section->getNormalizedVersion($this->id);
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

    public function getUniqueSyllabiCount ()
    {
        $syllabi = [];
        foreach ($this->syllabusVersions as $sv)
        {
            $syllabi[$sv->syllabus->id] = $sv->syllabus->id;
        }

        return count($syllabi);
    }

    public function getParentOrganization ()
    {
        $organization = null;
        foreach ($this->syllabusVersions as $sv)
        {
            if ($sv->syllabus->templateAuthorizationId)
            {
                list($type, $id) = explode('/', $sv->syllabus->templateAuthorizationId);

                switch ($type)
                {
                    case 'departments':
                        $organization = $this->getSchema('Syllabus_AcademicOrganizations_Department')->get($id);
                        break;
                    case 'colleges':
                        $organization = $this->getSchema('Syllabus_AcademicOrganizations_College')->get($id);
                        break;
                    default:
                        break;
                }
            }
        }

        return $organization;
    }
}



