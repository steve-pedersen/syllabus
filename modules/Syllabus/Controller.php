<?php

/**
 * Handles main business logic for entity/user syllabi.
 * 
 * @author      Steve Pedersen <pedersen@sfsu.edu>
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Syllabus_Controller extends Syllabus_Master_Controller {

    public static function getRouteMap ()
    {
        return [
            'syllabi'                           => ['callback' => 'mySyllabi'],
            'syllabus/:id'                      => ['callback' => 'edit',  ':id' => '[0-9]+|new'],
            'syllabus/courses'                  => ['callback' => 'courseLookup', ':id' => '[0-9]+'],
            // 'syllabus/:id/:version'             => ['callback' => 'view',  ':id' => '[0-9]+|new'],
        	// 'syllabus/entity/:eid'              => ['callback' => 'list', 	':eid' => '[0-9]+'],
        	// 'syllabus/entity/:eid/view/:sid'    => ['callback' => 'view', 	':eid' => '[0-9]+', ':sid' => '[0-9]+'],
        	// 'syllabus/entity/:eid/edit/:sid'    => ['callback' => 'edit', 	':eid' => '[0-9]+', ':sid' => '[0-9]+|new'],
            // 'syllabus/entity/:eid/render/:sid'  => ['callback' => 'render',':eid' => '[0-9]+', ':sid' => '[0-9]+'],
        ];
    }

    public function mySyllabi ()
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $mySyllabi = $syllabi->find($syllabi->createdById->equals($viewer->id), ['orderBy' => '-createdDate']);

        // TODO: update header vars based on which view mode we are in
        $title = 'My Syllabi';
        $subtitle = 'Overview';
        $description = 'Temporary layout';
        $this->setPageTitle($title);
        $this->buildHeader('partial:_header.html.tpl', $title, $subtitle, $description);

        $this->template->syllabi = $mySyllabi;
    }

    public function edit ()
    {
        $viewer = $this->requireLogin();
        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        $sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id', ['allowNew' => true]);

        $data = $this->request->getPostParameters();
        if (isset($data['syllabusVersion']) && isset($data['syllabusVersion']['id']))
        {
            $syllabusVersion = $syllabusVersions->get($data['syllabusVersion']['id']);
        }
        else
        {
            $syllabusVersion = $syllabus->latestVersion ?? $syllabusVersions->createInstance();
        }
        $sectionExtensions = $syllabusVersion->getSectionExtensions();

        $title = ($syllabus->inDatasource ? 'Edit' : 'Create') . ' Syllabus';
        $this->setPageTitle($title);
        $this->buildHeader('partial:_header.edit.html.tpl', $title, $syllabusVersion->title, $syllabusVersion->description);

        if ($this->request->wasPostedByUser())
        {      
            switch ($this->getPostCommand()) {

                case 'addsection':
                	if (($sectionClass = $data['addSection']) !== 'false')
                	{
	                    $realSection = $this->schema($sectionClass)->createInstance();
	                    $realSectionExtension = $sectionVersions->createInstance()->getExtensionByName($sectionClass);

	                    if ($realSectionExtension::getExtensionName() === 'course')
	                    {
	                        $currentCourses = $viewer->classDataUser->getCurrentEnrollments();
	                    }

	                    $this->template->realSection = $realSection;
	                    $this->template->realSectionClass = $sectionClass;
	                    $this->template->sectionExtension = $realSectionExtension;
                	}
                	else
                	{
                		$this->flash('You must choose a section type from the dropdown list.', 'danger');
                	}

                    break;

                case 'editsection':
                    $sectionVersion = $sectionVersions->get(key($this->getPostCommandData()));
                    $genericSection = $sectionVersion->section;
                    $test = [];
                    foreach ($data['section']['properties'] as $key => $prop)
                    {
                        $genericSection->$key = ($key==='isAnchored' || $key==='readOnly') ? (bool)$prop[$sectionVersion->id] : $prop[$sectionVersion->id];
                        $test[] = ($key==='isAnchored' || $key==='readOnly') ? (bool)$prop[$sectionVersion->id] : $prop[$sectionVersion->id];
                    }
                    
                    $genericSection->isAnchored = ($genericSection->isAnchored === null) ? true : $genericSection->isAnchored;
                    $realSection = $sectionVersion->resolveSection();
                    
                    $this->template->realSection = $realSection;
                    $this->template->realSectionClass = get_class($realSection);
                    $this->template->sectionExtension = $sectionVersion->getExtensionByName(get_class($realSection));
                    $this->template->genericSection = $genericSection;
                    $this->template->currentSectionVersion = $sectionVersion;
                    break;

                case 'editsyllabus':
                    $this->template->editMetadata = true;
                    $this->template->syllabusVersion = $syllabusVersion;
                    break;

                case 'saveorder':
                	// echo "<pre>"; var_dump($this->request->getPostParameters()); die;
                	// break;

                case 'savesection':
                case 'savesyllabus':
                    $this->saveSyllabus($syllabus);
                    break;
            }
        }

        $this->template->sidebarMinimized = true;
        $this->template->title = $title;
        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sections = $syllabusVersion->getSections(true);
        // $this->template->sections = $syllabus->getSections(true);
        $this->template->sectionExtensions = $sectionExtensions;
        $this->template->userCourses = $currentCourses ?? $viewer->classDataUser->getCurrentEnrollments(); // TODO: Add only if needed
    }

    /**
     * Bump syllabus and section versions--creating new instances of generic, real, and subsections.
     */  
    protected function saveSyllabus ($syllabus)
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        $sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        $subsections = $this->schema('Syllabus_Syllabus_Subsection');

        $data = $this->request->getPostParameters();
        $anyChange = false;
        $sectionChange = false;

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
            $syllabus->createdDate = new DateTime;
            $syllabus->createdById = $viewer->id;
        }
        $syllabus->save();

        // save section data
        $prevSectionVersion = null;
        if (isset($data['section']) && $sectionVersionId)
        {
            $anyChange = true;
            $sectionChange = true;
            $realSectionClass = $data['section']['realClass'][$sectionVersionId];
            $extKey = $data['section']['extKey'][$sectionVersionId];

            if ($sectionVersionId === 'new')
            {
                $genericSection = $sections->createInstance();
                $genericSection->createdDate = new DateTime;
                $genericSection->createdById = $viewer->id;
            }
            else
            {
                $prevSectionVersion = $sectionVersions->get($sectionVersionId);
                $genericSection = $prevSectionVersion->section;
                $genericSection->modifiedDate = new DateTime;
            }
            $genericSection->save();   

            $realSection = $this->schema($realSectionClass)->createInstance();
            $realSection->absorbData($data['section']['real']);
            if ($extKey === 'course_id')
            {
            	$realSection->externalKey = $data['section']['real']['external_key'];
            }
            $realSection->save();

            // TODO: find out if single course section can have multiple associated syllabi
            if ($extKey === 'course_id' && $realSection->externalKey)
            {
                $courses = $this->schema('Syllabus_ClassData_CourseSection');
                $course = $courses->findOne($courses->id->equals($realSection->externalKey));
                $course->syllabus_id = $syllabus->id ?? '';
                $course->save();
            }

            // TODO: add Subsection logic
            // $subsection = $subsections->createInstance(); 

            $newSectionVersion = $sectionVersions->createInstance();
            $newSectionVersion->createdDate = new DateTime;
            $newSectionVersion->sectionId = $genericSection->id;
            $newSectionVersion->$extKey = $realSection->id;
            if (isset($data['section']['generic'][$sectionVersionId]))
            {
                $newSectionVersion->absorbData($data['section']['generic'][$sectionVersionId]);
            }
            $newSectionVersion->save();
        }

        // save title/description & bump syllabus version
        if (isset($data['syllabus']) || isset($data['syllabusVersion']) || isset($data['section']))
        {
            $anyChange = true;
            if (isset($data['syllabusVersion']) && isset($data['syllabusVersion']['id']))
            {
            	$oldSyllabusVersion = $syllabusVersions->get($data['syllabusVersion']['id']);
            	if ($oldSyllabusVersion->syllabus->id !== $syllabus->id)
            	{
            		$oldSyllabusVersion = null;
            	}
            }
            else
            {
            	$oldSyllabusVersion = $syllabus->latestVersion;
            }
            
            if ($oldSyllabusVersion)
            {
            	$newSyllabusVersion = $oldSyllabusVersion->createDerivative();
            }
            else
            {
	            $newSyllabusVersion = $syllabusVersions->createInstance();
	            $newSyllabusVersion->createdDate = new DateTime;
	            $newSyllabusVersion->syllabusId = $syllabus->id;            	
            }
   
            if (isset($data['syllabus']) && isset($data['syllabus']['title']))
            {
                $newSyllabusVersion->absorbData($data['syllabus']);
            }

            $newSyllabusVersion->save();
        }

        // map any bumped section versions to this new syllabus version
        if ($sectionChange && $sectionVersionId)
        {   
            $anyChange = true;
            $oldCount = count($oldSyllabusVersion->sectionVersions);
            $newCount = count($newSyllabusVersion->sectionVersions);

            // if editing a section, remove it's previous version from the new syllabus version
            if ($prevSectionVersion && ($oldCount === $newCount))
            {
            	$newSyllabusVersion->sectionVersions->remove($prevSectionVersion);
            }

            // add new section version to this new syllabus version
            $defaultPosition = $newSyllabusVersion->sectionCount + 1;
            $newSyllabusVersion->sectionVersions->add($newSectionVersion);
            $newSyllabusVersion->sectionVersions->setProperty($newSectionVersion, 'sort_order', 
            	isset($data['section']['properties']['sortOrder']) ? $data['section']['properties']['sortOrder'][$sectionVersionId] : $defaultPosition);
            $newSyllabusVersion->sectionVersions->setProperty($newSectionVersion, 'read_only', 
            	isset($data['section']['properties']['readOnly']) ? $data['section']['properties']['readOnly'][$sectionVersionId] : false);
            $newSyllabusVersion->sectionVersions->setProperty($newSectionVersion, 'is_anchored', 
            	isset($data['section']['properties']['isAnchored']) ? $data['section']['properties']['isAnchored'][$sectionVersionId] : true);
            $newSyllabusVersion->sectionVersions->setProperty($newSectionVersion, 'log', 
            	isset($data['section']['properties']['log']) ? $data['section']['properties']['log'][$sectionVersionId] : '');
            $newSyllabusVersion->save();
            $newSyllabusVersion->sectionVersions->save();
        }

        // save sort_order posted data
        if ($oldSyllabusVersion && $newSyllabusVersion)
        {
            foreach ($newSyllabusVersion->sectionVersions as $sv)
            {              	
                if (isset($data['section']['properties']['sortOrder']) && isset($data['section']['properties']['sortOrder'][$sv->id]))
                {
                	$anyChange = true;
                    $newSyllabusVersion->sectionVersions->setProperty($sv, 'sort_order', $data['section']['properties']['sortOrder'][$sv->id]);
                }
            }
            $newSyllabusVersion->save();
            $newSyllabusVersion->sectionVersions->save();
        }

        if (!$anyChange) 
        { 
            $this->flash('No changes were made', 'warning');
        }

        $this->response->redirect('syllabus/' . $syllabus->id);
    }

    public function courseLookup ()
    {
        $this->requireLogin();
        if ($courseId = $this->request->getQueryParameter('courses'))
        {
            $courses = $this->schema('Syllabus_ClassData_CourseSection');
            $course = $courses->findOne($courses->id->equals($courseId));

            if ($course)
            {
                $results = [
                    'message' => 'Course found.',
                    'status' => 'success',
                    'data' => $course->getData()
                ];
                $results['data']['external_key'] = $results['data']['id'];
            }
            else
            {
                $results = [
                    'message' => 'No courses found.',
                    'status' => 'error',
                    'data' => ''
                ];
            }

            echo json_encode($results);
            exit;
        }
    }

    /**
     * Viewing in thumbnail mode will make requests to Screenshotter service
     * to fetch images of all syllabi associated with this entity.
     */
    public function list ()
    {
        // $account = $this->requireLogin();
        $app = $this->getApplication();
        $eid = $this->getRouteVariable('eid');
        $eid = 999;
        $urls = [];
        $messages = [];
        $cachedVersions = true;
        // $keyPrefix = "{$eid}-";

        // get all syllabus ids for this entity based on offset and limit=10
        // $sids = ['1', '6', '4', '7', '3', '8', '5', '9', '2', '10'];
        $sids = ['1', '6', '4'];

        // generate some key/value access tokens for each render page
        $keyPrefix = "{$eid}-";
        $screenshotter = new Syllabus_Services_Screenshotter($app);
        $screenshotter->saveUids($eid, $sids);

        foreach ($sids as $id)
        {
            $urls[$id] = $this->baseUrl("syllabus/entity/{$eid}/render/{$id}");
        }

        $results = $screenshotter->concurrentRequests($urls, $cachedVersions, $keyPrefix);
        $results = json_decode($results);

        $this->template->messages = $results->messages;
        $this->template->urls = $results->imageUrls;
    }

    /**
     * Accessible only with proper Access-Token
     */    
    public function render ()
    {
        // $entities = $this->schema('Syllabus_Academia_Entity');
        // $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $tokenHeader = $this->requireExists($this->request->getHeader('Access-Token'));
        $eid = $this->getRouteVariable('eid');
        $sid = $this->getRouteVariable('sid');
        $key = "{$eid}-{$sid}";
        $uid = Syllabus_Services_Screenshotter::CutUid($key);

        if ($tokenHeader && $uid && ($tokenHeader == $uid))
        {
            // NOTE: Temporary debug ***************
            // if (!$this->requireExists($syllabi->get($sid)) || !$this->requireExists($entities->get($eid)))
            if (intval($sid) > 5) {
                // $this->notFound();
                http_response_code (404);
                die;
            } else {
                $this->template->syllabusId = $sid;
            }
        }
        elseif (!$tokenHeader && !$uid)
        {

        }
        else
        {
            http_response_code (401);
            die;            
        }
    }


    public function view ()
    {
        // $account = $this->requireLogin();
        $eid = $this->getRouteVariable('eid');
        $sid = $this->getRouteVariable('sid');
        $sidInt = intval($sid);

        // NOTE: Temporary debug ***************
        // do a requireExists() here
        if ($sidInt > 5) {
            // $this->notFound();
            $response_code = 404;
            http_response_code (404);
            die;
        } else {
            $this->template->syllabusId = $sid;
        }
    }


}