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

    public function setTitle ($title)
    {      
        $title = (is_string($title) ? strip_tags(trim($title)) : null);
        $this->_assign('title', $title);
        
        if (empty($title))
        {
            $this->invalidate('title', 'Please provide a title for your section.');
        }
    }
    public function setDescription ($description)
    {        
        $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
        $description = (is_string($description) ? $htmlSanitizer->sanitize(trim($description)) : null);
        $this->_assign('description', $description);
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

    public function getExtensionByRecord ($record)
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

    public function getThisExtension ()
    {
        return $this->getExtensionByRecord(get_class($this->resolveSection()));
    }

    public function getExtensionByName ($name)
    {
        $ext = null;
        $moduleManager = $this->getApplication()->moduleManager;
        $extensions = $this->_schema->getExtensions();
        foreach ($extensions as $ext)
        {
            if ($ext->getExtensionName() === $name)
            {
                $ext = $moduleManager->getExtensionByName($ext::getExtensionPointName(), $ext::getExtensionName());
                break;
            }
        }
        return $ext;
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

    public function processEdit ($request, $data=null)
    {
        if ($realSection = $this->resolveSection())
        {
            $realSection->processEdit($request, $data);
        }
    }

    public function getHierarchyInformation ()
    {
        $info = [];
        foreach ($this->syllabusVersions as $sv)
        {
            if ($sv->syllabus->templateAuthorizationId)
            {
                $info[$sv->syllabus->id] = [];
                $info[$sv->syllabus->id]['syllabusId'] = $sv->syllabus->id;
                $info[$sv->syllabus->id]['sectionVersionId'] = $this->id;
                $info[$sv->syllabus->id]['templateAuthorizationId'] = $sv->syllabus->templateAuthorizationId;
            }
        }

        return $info;
    }

    public function getOwnerOrganization ()
    {
        $organization = null;
        $allOrganizationOwners = $this->getHierarchyInformation();
        $owner = array_pop($allOrganizationOwners);
        
        if ($owner['templateAuthorizationId'])
        {
            list($type, $id) = explode('/', $owner['templateAuthorizationId']);

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

        return $organization;
    }

    // TODO: Re-test this for other types of inheritance (e.g. from user to user)
    // $organization is set when user is editing an org template
    public function canEdit ($viewer, $syllabusVersion, $organization=null)
    {
        $siteSettings = $this->getApplication()->siteSettings;
        $adminUserId = $siteSettings->getProperty('university-template-user-id');
        $sectionOwnerOrganization = $this->getOwnerOrganization();
        $adHocEditor = false;
        if ($adHocRoles = $syllabusVersion->syllabus->getAdHocRoles())
        {
            foreach ($adHocRoles as $role)
            {
                if (isset($role['users']))
                {
                    foreach ($role['users'] as $user)
                    {
                        if ($user->id === $viewer->id)
                        {
                            $adHocEditor = true;
                            break;
                        }
                    }                    
                }
                if ($adHocEditor) break;
            }
        }

        if (!$this->readOnly)
        {
            $this->canEditReadOnly = true;
        }
        // Section is part of Base Template and WAS created by this $viewer
        elseif ($viewer->id == $adminUserId && $viewer->id === $this->section->createdById)
        {   
            $this->canEditReadOnly = true;
        }
        // Section is part of Base Template and WAS NOT created by this $viewer
        elseif ($this->section->createdById == $adminUserId && $this->section->createdById !== $viewer->id)
        {   
            $this->canEditReadOnly = false;
        }
        // Section is part of a Dept or College template and WAS created by this $organization
        elseif ($organization && (!$sectionOwnerOrganization || 
                ($organization->templateAuthorizationId === $sectionOwnerOrganization->templateAuthorizationId)))
        {   
            $this->canEditReadOnly = $organization->userHasRole($viewer, 'creator') || 
                $organization->userHasRole($viewer, 'manager');
        }
        // Section is part of a Dept or College template and WAS NOT created by this organization or user
        elseif ($sectionOwnerOrganization)
        {   
            $this->canEditReadOnly = false;
        }
        // Allow ad hoc editors to edit 
        elseif ($adHocEditor)
        {
            $this->canEditReadOnly = true;
        }
        // Section is editable if it was created by $viewer
        else
        {   
            $this->canEditReadOnly = $this->section->createdById === $viewer->id;
        }

        return $this->canEditReadOnly;
    }

}

