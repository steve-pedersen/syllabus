<?php

/**
 */
class Syllabus_Organizations_GroupController extends Syllabus_Organizations_BaseController
{
    private $_organization;
    private $_routeBase;

    public static function getRouteMap ()
    {
        return [
        	'/groups'             		=> ['callback' => 'listOrganizations'],
        	'/groups/:oid'		   		=> ['callback' => 'dashboard', ':oid' => '[0-9]+'],
        	'/groups/:oid/edit'		   	=> ['callback' => 'manageGroup', ':oid' => '[0-9]+|new'],
        	'/groups/:oid/manage'    	=> ['callback' => 'manageOrganization', ':oid' => '[0-9]+'],
            '/groups/:oid/settings'  	=> ['callback' => 'manageOrganization', ':oid' => '[0-9]+'],
        	'/groups/:oid/users'     	=> ['callback' => 'manageUsers', ':oid' => '[0-9]+'],
            '/groups/:oid/users/:uid'	=> ['callback' => 'editUser', ':oid' => '[0-9]+'],
            '/groups/:oid/sections'		=>	['callback' => 'manageSections', ':oid' => '[0-9]+'],
            '/groups/:oid/sections/edit' =>	['callback' => 'editSection', ':oid' => '[0-9]+'],
        ];
    }

	public function getOrganization ($id=null)
	{
		$schema = $this->getOrganizationSchema();
        return is_numeric($id) ? $schema->get($id) : $schema->createInstance();
	}

    public function getOrganizationSchema () { return $this->schema($this->getSchemaName()); }
    
    public function getSchemaName () { return 'Syllabus_Organizations_Group'; }

    public function listOrganizations ()
    {
    	$this->requirePermission('admin');
    	$this->template->organizations = $this->getOrganizationSchema()->getAll();
    	$this->template->groups = true;
    }

    public function dashboard ()
    {
    	parent::dashboard();
    	$this->_organization = $this->getOrganization($this->getRouteVariable('oid'));
    	$this->_organization->requireRole('manager', $this);

    	$this->template->group = true;
    }

    public function manageGroup ()
    {
    	$this->_organization = $this->getOrganization($this->getRouteVariable('oid'));
    	$this->_organization->requireRole('manager', $this);

        if ($this->request->wasPostedByUser())
        {
            $data = $this->request->getPostParameters();

            switch ($this->getPostCommand())
            {
                case 'save':
                	$this->_organization->absorbData($data);
                	$this->_organization->modifiedDate = new DateTime;
                	$this->_organization->createdDate = isset($this->_organization->id) ? 
                		$this->_organization->createdDate : new DateTime;
                	$this->_organization->save();

                    $this->flash('Saved');
                    $this->response->redirect('groups');
                    break;

                case 'delete':
                	// $group->delete();
                    $this->flash('Deleted.');
                    $this->response->redirect('groups');
                    break;
            }
        }

    	$this->template->group = $this->_organization;
    }

    public function manageSections ()
    {
    	$this->_organization = $this->getOrganization($this->getRouteVariable('oid'));
    	$this->_organization->requireRole('creator', $this);
    	$this->_routeBase = 'groups/' .$this->_organization->id . '/';
        $this->addBreadcrumb($this->_routeBase, $this->_organization->name);
        $this->addBreadcrumb($this->_routeBase . 'sections', 'Manage Sections');
    	$importables = $this->schema('Syllabus_Syllabus_ImportableSection');
    	$sectionExtensions = $this->schema('Syllabus_Syllabus_SyllabusVersion')->createInstance()->getSectionExtensions();
    	$importableSections = $importables->find(
    		$importables->organizationId->equals('groups/' . $this->_organization->id)
    	);
    	$sortedImportables = [];
    	foreach ($importableSections as $importable)
    	{
    		$type = $importable->getType(true);
    		if (!isset($sortedImportables[$type]))
    		{
    			$sortedImportables[$type] = [];
    		}
    		$sortedImportables[$type][] = $importable;
    	}

    	$this->template->group = $this->_organization;
    	$this->template->sectionExtensions = $sectionExtensions;
    	$this->template->sortedImportableSections = $sortedImportables;
    }

    public function editSection ()
    {
    	$this->_organization = $this->getOrganization($this->getRouteVariable('oid'));
    	$this->_organization->requireRole('creator', $this);
    	$this->_routeBase = 'groups/' .$this->_organization->id . '/';
        $this->addBreadcrumb($this->_routeBase, $this->_organization->name);
        $this->addBreadcrumb($this->_routeBase . 'sections', 'Manage Sections');
        $this->addBreadcrumb($this->_routeBase . 'sections/edit', 'Edit');

    	$sections = $this->schema('Syllabus_Syllabus_Section');
    	$sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        $importables = $this->schema('Syllabus_Syllabus_ImportableSection');
  		
  		$sectionId = $this->request->getQueryParameter('s');
  		$importable = $sectionId ? $importables->get($sectionId) : $importables->createInstance();
  		$type = $this->request->getQueryParameter('type');
  		$realSectionExtension = $sectionVersions->createInstance()->getExtensionByName($type);
        // echo "<pre>"; var_dump($realSectionExtension->getDisplayName()); die;
  		$realSectionClass = $realSectionExtension->getRecordClass();
  		$realSection = !$importable->inDataSource ? 
  			$this->schema($realSectionClass)->createInstance() : $importable->section->latestVersion->resolveSection();

  		if ($this->request->wasPostedByUser())
  		{
  			$data = $this->request->getPostParameters();
  			switch ($this->getPostCommand())
  			{
  				case 'savesection':
                    if ($importable && $realSection)
                    {
                        $key = $realSectionExtension->getExtensionName();
                        foreach ($importable->section->latestVersion->resolveSection()->$key as $item)
                        {
                            if (!isset($data['section']['real'][$item->id]))
                            {
                                $item->delete();
                            }
                        }
                    }
                    $realSection = $this->schema($realSectionClass)->createInstance();
  					$errorMsg = $realSection->processEdit($this->request, $data);
  					if ($errorMsg === '')
  					{
						$section = $importable->inDataSource ? $importable->section : $sections->createInstance();
						$section->createdDate = $section->createdDate ?? new DateTime;
						$section->modifiedDate = new DateTime;
						$section->save();

						$key = $realSectionExtension->getExtensionKey();
						$sectionVersion = $importable->inDataSource ? 
							$section->latestVersion : $sectionVersions->createInstance();
						$sectionVersion->title = 'Importable section content - ' . $realSectionExtension->getDisplayName();
						$sectionVersion->sectionId = $section->id;
						$sectionVersion->createdDate = new DateTime;
						$sectionVersion->$key = $realSection->id;
						$sectionVersion->save();

						$importable->title = $data['section']['title'];
						$importable->organizationId = 'groups/' . $this->_organization->id;
						$importable->createdDate = $importable->createdDate ?? new DateTime;
						$importable->modifiedDate = new DateTime;
						$importable->importable = $data['importable'] && $data['importable'] === '1';
						$importable->externalKey = $realSectionExtension->getExtensionKey();
						$importable->section_id = $section->id;
						$importable->save();

						$this->flash('Section saved');
  					}
  					else
  					{
  						$this->flash($errorMsg);
  					}
  					$this->response->redirect('groups/' . $this->_organization->id . '/sections');

  					break;

  				case 'deleteitem':

                    $realSectionItemClass = key($this->getPostCommandData());
                    $deleteId = key($this->getPostCommandData()[$realSectionItemClass]);
                    $item = $this->schema($realSectionItemClass)->get($deleteId);
                    $item->delete();
                    $this->flash('Deleted.');
  					break;
  			}
  		}

        $campusResources = $this->schema('Syllabus_Syllabus_CampusResource');
    	$this->template->campusResources = $campusResources->find(
            $campusResources->deleted->isFalse()->orIf($campusResources->deleted->isNull()),
            ['orderBy' => ['sortOrder', 'title']]
        );
        $this->template->importable = $importable;
        $this->template->realSection = $realSection;
        $this->template->realSectionClass = $realSectionClass;
        $this->template->sectionExtension = $realSectionExtension;
        $this->template->group = $this->_organization;
    }




    private function saveSection ($paramData)
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        $sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        $subsections = $this->schema('Syllabus_Syllabus_Subsection');

        $data = $paramData ?? $this->request->getPostParameters();
        $anyChange = false;
        $sectionChange = false;
        $sectionDelete = false;

        // echo "<pre>"; var_dump($data['section']['properties']); die;
        if ((isset($data['section']) && isset($data['section']['versionId'])))
        {
        	$sectionVersionId = $data['section']['versionId'];
        }
        else
        {
        	$postCommandData = $this->getPostCommandData();
        	$sectionVersionId = ($postCommandData && is_array($postCommandData)) ? key($postCommandData) : null;
        }

        // save syllabus metadata
        if ($syllabus->inDataSource)
        {
            $syllabus->modifiedDate = new DateTime;
        }
        else
        {
            $syllabus->createdDate = $syllabus->modifiedDate = new DateTime;
            if (!$syllabus->templateAuthorizationId)
            {
                $syllabus->createdById = $viewer->id;
            }
        }
        $syllabus->save();

        // save section data
        $prevSectionVersion = null;
        if (isset($data['section']) && $sectionVersionId)
        {
            $anyChange = true;
            $sectionChange = true;
            if (!isset($data['section']['realClass'][$sectionVersionId]))
            {
                $this->accessDenied('Incorrect section version id.');
            }
            $realSectionClass = $data['section']['realClass'][$sectionVersionId];
            $extKey = $data['section']['extKey'][$sectionVersionId];

            if ($sectionVersionId === 'new')
            {
                $genericSection = $sections->createInstance();
                $genericSection->createdDate = new DateTime;
                $genericSection->createdById = $viewer->id;
                $genericSection->save();
            }
            else
            {
                $prevSectionVersion = $sectionVersions->get($sectionVersionId);

                if (!($oldSyllabusVersion = $syllabusVersions->get($data['syllabusVersion']['id'])))
                {
                    $this->accessDenied('Incorrect syllabus version id.');
                }
                $prevSection = $prevSectionVersion->section;
                $hasSection = false;
                foreach ($oldSyllabusVersion->sectionVersions as $sectionVersion)
                {  
                    if ($sectionVersion->section->id === $prevSection->id)
                    {
                        $hasSection = true;
                        break;
                    }
                }
                if (!$hasSection)
                {
                    $this->accessDenied('Incorrect section version id.');
                }

                if ($this->isInheritedSection($prevSectionVersion, $syllabus->templateAuthorizationId) ||
                    $this->isUpstreamSection($prevSectionVersion, $syllabus, $viewer))
                {
                    // echo "<pre>"; var_dump('yo', $syllabus->templateAuthorizationId); die;
                    $genericSection = $sections->createInstance();
                    $genericSection->createdDate = new DateTime;
                    $genericSection->createdById = $viewer->id;
                    $genericSection->save();
                }
                else
                {
                    $genericSection = $prevSectionVersion->section;
                    $genericSection->modifiedDate = new DateTime;
                    $genericSection->save();              
                }
            }
             
            $realSection = $this->schema($realSectionClass)->createInstance();
            $errorMsg = $realSection->processEdit($this->request, $data);
            if ($errorMsg && $errorMsg !== '')
            {
            	$this->template->addUserMessage($errorMsg, '');
            }

            // TODO: add Subsection logic
            // $subsection = $subsections->createInstance(); 

            $newSectionVersion = $sectionVersions->createInstance();
            $newSectionVersion->createdDate = new DateTime;
            $newSectionVersion->sectionId = $genericSection->id;
            $newSectionVersion->$extKey = $realSection->id;
            if (isset($data['section']['generic'][$sectionVersionId]))
            {
                // $newSectionVersion->absorbData($data['section']['generic'][$sectionVersionId]);
                $newSectionVersion->title = $data['section']['generic'][$sectionVersionId]['title'];
                $newSectionVersion->description = $data['section']['generic'][$sectionVersionId]['description'];
            }
            $newSectionVersion->save();
        }
    }
}