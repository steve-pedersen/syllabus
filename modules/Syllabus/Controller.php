<?php

// BUG: app.js:4 Uncaught CKEditor is not initialized yet, use ckeditor() with a callback.

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
            'syllabi'                   => ['callback' => 'mySyllabi'],
            'syllabus/:id'              => ['callback' => 'edit', ':id' => '[0-9]+|new'],
            'syllabus/:id/view'         => ['callback' => 'view', ':id' => '[0-9]+'],
            'syllabus/:id/screenshot'   => ['callback' => 'screenshot', ':id' => '[0-9]+'],
            'syllabus/courses'          => ['callback' => 'courseLookup'],
            'syllabus/start'            => ['callback' => 'start'],
            'syllabus/startwith/:id'    => ['callback' => 'startWith', ':id' => '[0-9]+'],
        ];
    }

    public function mySyllabi ()
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $courseSections = $this->schema('Syllabus_ClassData_CourseSection');
        $offset = 0;
        $limit = 6;

        $siteSettings = $this->getApplication()->siteSettings;
        $userId = $siteSettings->getProperty('university-template-user-id');
        $templateId = $siteSettings->getProperty('university-template-id');
        if (($viewer->id != $userId) && !$this->hasPermission('admin'))
        {
            $this->requireExists($templateId);
        }
        $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());

        switch ($mode = $this->request->getQueryParameter('mode', 'overview')) {

            case 'courses':
                
                $myCourses = $viewer->classDataUser->getCurrentEnrollments();
                $courses = [];
                foreach ($myCourses as $i => $courseSection)
                {
                    $index = $i % 5;
                    $courseSyllabus = $syllabi->get($courseSection->syllabus_id);
                    $courseSection->courseSyllabus = $courseSyllabus;
                    $courseSection->createNew = $courseSyllabus ? false : true;
                    $courseSection->pastCourseSyllabi = $courseSection->getRelevantPastCoursesWithSyllabi($viewer);
                    $courses[$courseSection->term][] = $courseSection;
                    
                    $imageUrl = "assets/images/testing0$index.jpg";
                    if ($courseSyllabus)
                    {
                        $sid = $courseSyllabus->id;
                        $results = $this->getScreenshotUrl($sid, $screenshotter);
                        $imageUrl = $results->imageUrls->$sid;
                    }
                    $courseSection->imageUrl = $imageUrl;
                }

                $this->template->allCourses = $courses;
                break;
            
            case 'submissions':            
                
                break;

            case 'overview':
            default:
                $campusResources = $this->schema('Syllabus_Syllabus_CampusResource');
                
                $userSyllabi = $syllabi->find(
                    $syllabi->createdById->equals($viewer->id)->andIf($syllabi->templateAuthorizationId->isNull()), 
                    ['orderBy' => ['-modifiedDate', '-createdDate'], 'limit' => $limit, 'offset' => $offset]
                );
                
                foreach ($userSyllabi as $userSyllabus)
                {
                    $sid = $userSyllabus->id;
                    $results = $this->getScreenshotUrl($sid, $screenshotter);
                    $userSyllabus->imageUrl = $results->imageUrls->$sid;
                }
                $this->template->syllabi = $userSyllabi;
                $this->template->campusResources = $campusResources->find(
                    $campusResources->deleted->isFalse()->orIf($campusResources->deleted->isNull()),
                    ['orderBy' => ['sortOrder', 'title']]
                );

                break;
        }

        if ($this->request->wasPostedByUser())
        {    
            $courseSection = $courseSections->get(key($this->getPostCommandData()));
            $data = $this->request->getPostParameters();

            $universityTemplate = $this->requireExists($syllabi->get($templateId));
            $syllabus = $this->startWith($universityTemplate, true, true);

            switch ($this->getPostCommand()) {
                
                case 'courseNew':
                    list($success, $newSyllabusVersion) = $this->createCourseSyllabus($syllabus->id, $courseSection);
                    if ($success)
                    {
                        $this->flash('Your new course syllabus is ready for you to edit and add more sections.', 'success');
                        $this->response->redirect('syllabus/' . $newSyllabusVersion->syllabus->id);
                    }
                    break;

                case 'courseClone':
                    $pastSyllabus = isset($data['courseSyllabus']) ? $syllabi->get(isset($data['courseSyllabus'])) : null;
                        
                    if ($pastSyllabus && $this->hasCloningPermission($pastSyllabus, $viewer))
                    {
                        $newSyllabus = $this->startWith($pastSyllabus, true);
                        $newSyllabusVersion = $this->updateCourseSyllabus($pastSyllabus, $newSyllabus, $courseSection);
                        
                        $this->flash('Your new course syllabus is ready for you to edit.', 'success');
                        $this->response->redirect('syllabus/' . $newSyllabusVersion->syllabus->id);
                    }

                    break;

                default:

            }
        }

        $this->template->mode = $mode;      
    }


    public function start ()
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $courseSections = $this->schema('Syllabus_ClassData_CourseSection');
        $departmentSchema = $this->schema('Syllabus_AcademicOrganizations_Department');
        $collegeSchema = $this->schema('Syllabus_AcademicOrganizations_College');
        
        $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
        $siteSettings = $this->getApplication()->siteSettings;
        $userId = $siteSettings->getProperty('university-template-user-id');
        $templateId = $siteSettings->getProperty('university-template-id');
        if (($viewer->id != $userId) && !$this->hasPermission('admin'))
        {
            $this->requireExists($templateId);
        }
        elseif (!$templateId && $this->hasPermission('admin'))
        {
            $this->template->pStartFromNothing = true;
        }

        // if based off of a course
        if ($courseSectionId = $this->request->getQueryParameter('course'))
        {
            $courseSection = $courseSections->get($courseSectionId);
            $this->template->courseSection = $courseSection;
            $pastCourseSyllabi = $courseSection->getRelevantPastCoursesWithSyllabi($viewer);
            foreach ($pastCourseSyllabi as $courseSyllabus)
            {
                $sid = $courseSyllabus->id;
                $results = $this->getScreenshotUrl($sid, $screenshotter);
                $courseSyllabus->imageUrl = $results->imageUrls->$sid;
            }
            $this->template->pastCourseSyllabi = $pastCourseSyllabi;
        }
        else
        {
            $userSyllabi = $syllabi->find(
                $syllabi->createdById->equals($viewer->id), 
                ['orderBy' => ['-modifiedDate', '-createdDate'], 'limit' => 4]
            );        
            foreach ($userSyllabi as $userSyllabus)
            {
                $sid = $userSyllabus->id;
                $results = $this->getScreenshotUrl($sid, $screenshotter);
                $userSyllabus->imageUrl = $results->imageUrls->$sid;
            }
            $this->template->syllabi = $userSyllabi;
        }

        $orgs = [];
        $templates = [];
        foreach ($viewer->classDataUser->enrollments as $cs)
        {
            if (!isset($orgs[$cs->department->id]))
            {
                $templates[$cs->department->id] = $syllabi->find(
                    $syllabi->templateAuthorizationId->equals($cs->department->templateAuthorizationId),
                    ['orderBy' => ['-modifiedDate', '-createdDate'], 'limit' => '4']
                );
                foreach ($templates[$cs->department->id] as $template)
                {
                    $sid = $template->id;
                    $results = $this->getScreenshotUrl($sid, $screenshotter);
                    $template->imageUrl = $results->imageUrls->$sid;
                }
                $orgs[$cs->department->id] = $cs->department;
            }
        }
        $this->template->organizations = $orgs;
        $this->template->templates = $templates;


        if ($org = $this->getRouteVariable('organization'))
        {
            switch ($org->organizationType) 
            {
                case 'Department':
                    $this->template->templateAuthorizationId = $org->templateAuthorizationId;
                    $this->template->isTemplate = true;
                    break;
                default:
                    break;
            }
        }

        $pathParts = [];
        $pathParts[] = $this->getRouteVariable('routeBase');
        $pathParts[] = 'syllabus';

        if ($this->request->wasPostedByUser())
        {
            if ($this->getPostCommand() === 'start')
            {
                $data = $this->request->getPostParameters();
                $startingPoint = key($this->getPostCommandData());

                // TODO: update for 'clone' option
                $fromCourse = isset($courseSectionId) ? $courseSections->get($courseSectionId) : null;
                $templateAuthorizationId = isset($data['template']) ? $data['template'] : null;
                
                switch ($startingPoint) {
                    case 'university':                 
                        $startingTemplate = $syllabi->get($templateId);
                        break;
                    case 'department':
                    case 'college':
                    	$id = key($this->getPostCommandData()[$startingPoint]);
                    	$startingTemplate = $syllabi->get($id);
                        break;

                    default:
                        $this->flash('You must select a valid starting point.', 'danger');
                        $this->accessDenied('You must select a valid starting point.');
                        break;
                }
                $this->requireExists($startingTemplate);

                $syllabus = $this->startWith($startingTemplate, true, true);
                if (!$fromCourse)
                {
                    $version = $syllabus->latestVersion;
                }

                if ($fromCourse)
                {
                    list($success, $syllabusVersion) = $this->createCourseSyllabus($syllabus->id, $fromCourse);
                    if ($success)
                    {
                        $pathParts[] = $syllabusVersion->syllabus->id;
                        $pathParts = array_filter($pathParts);
                        // TODO: add language string check
                        $this->flash("
                            Your new syllabus draft includes all SF State requirements and course information for 
                            $courseSection->fullDisplayName. It is ready for you to edit.", 
                            'success'
                        );
                        $this->response->redirect(implode('/', $pathParts));
                    }
                }
                elseif ($templateAuthorizationId)
                {
                    $version = $syllabus->latestVersion;
                    $version->title = 'New syllabus template based on: ' . $version->title;
                    $version->description = 'You may change this metadata to describe your template better.';
                    $version->save();
                    $pathParts[] = $syllabus->id;
                    $pathParts = array_filter($pathParts);
                    $this->response->redirect(implode('/', $pathParts));
                }
                else
                {
                    $version = $syllabus->latestVersion;
                    $version->title = 'New syllabus based on: ' . $version->title;
                    $version->description = 'You may change this metadata to describe your syllabus better.';
                    $version->save();
                    $pathParts[] = $syllabus->id;
                    $pathParts = array_filter($pathParts);
                    // TODO: add language string check
                    $this->flash("
                        Your new syllabus draft includes all SF State requirements. You are able to choose which 
                        course it is for by adding a new 'Course Information' section.", 
                        'success'
                    );
                    $this->response->redirect(implode('/', $pathParts));
                }
            }            
        }
    }

    public function startWith ($fromSyllabus=null, $return=false, $baseTemplate=false)
    {
        $viewer = $this->requireLogin();
        if (!$fromSyllabus)
        {
            $fromSyllabus = $this->requireExists($this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id'));
        }
        
        if (!$baseTemplate && !$this->hasCloningPermission($fromSyllabus, $viewer))
        {
            $this->sendError(403, 'Forbidden', 'Non-Member', 'You must be a member of this organization in order to use this template.');
        }

        $pathParts = [];
        $pathParts[] = $this->getRouteVariable('routeBase');
        $pathParts[] = 'syllabus';

        $toSyllabus = $this->schema('Syllabus_Syllabus_Syllabus')->createInstance();
        $toSyllabus->createdDate = $toSyllabus->modifiedDate = new DateTime;
        $toSyllabus->createdById = $viewer->id;
        if ($tid = $this->getRouteVariable('templateAuthorizationId'))
        {
            $toSyllabus->templateAuthorizationId = $tid;
            $toSyllabus->createdById = null;
        }
        $toSyllabus->save();
        $pathParts[] = $toSyllabus->id;
        $pathParts = array_filter($pathParts);
        
        $toSyllabusVersion = $fromSyllabus->latestVersion->createDerivative(true);
        $toSyllabusVersion->syllabus_id = $toSyllabus->id;
        $toSyllabusVersion->save();
        $toSyllabusVersion->sectionVersions->save();
        // $toSyllabus->versions->add($toSyllabusVersion);

        if ($return)
        {
            return $toSyllabusVersion->syllabus;
        }

        $this->response->redirect(implode('/', $pathParts));
    }

    public function edit ()
    {
        $viewer = $this->requireLogin();
        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        $sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id', 
            ['allowNew' => $this->hasPermission('admin')]
        );

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
        $description = '';
        if ($syllabusVersion->inDataSource)
        {
            $description = 'v' . $syllabusVersion->normalizedVersion;
        }
        // $this->buildHeader('partial:_header.edit.html.tpl', $title, $syllabusVersion->title, $description);

        $routeBase = $this->getRouteVariable('routeBase', '');
        $organization = $this->getRouteVariable('organization', null);

        $pathParts = [];
        $pathParts[] = $this->getRouteVariable('routeBase');
        $pathParts[] = 'syllabus';

        $siteSettings = $this->getApplication()->siteSettings;
        $userId = $siteSettings->getProperty('university-template-user-id');
        $templateId = $siteSettings->getProperty('university-template-id');
        if ($templateId && $templateId == $syllabus->id)
        {
            $this->template->isUniversityTemplate = true;
        }
        elseif (!$templateId && $this->hasPermission('admin'))
        {
            $this->template->isDetachedSyllabus = true;
        }


        if ($this->request->wasPostedByUser())
        {      
            switch ($this->getPostCommand()) {

                case 'editsyllabus':
                    $this->template->editMetadata = true;
                    $this->template->syllabusVersion = $syllabusVersion;
                    break;

                case 'addsectionitem':

                    // TODO: Update for multiple open section items at once
                    $realSectionClass = key($this->getPostCommandData());
                    $realSection = $this->schema($realSectionClass)->createInstance();
                    $realSectionExtension = $sectionVersions->createInstance()->getExtensionByRecord($realSectionClass);
                    $extKey = $realSectionExtension->getExtensionKey();
       
					list($syllabusVersion, $newSectionVersionId) = $this->saveSection($syllabus, $syllabusVersion, $extKey, $organization);

                    $pathParts[] = $syllabusVersion->syllabus->id . '?edit=' . $newSectionVersionId;
                    $pathParts = array_filter($pathParts);
                    
                    $this->flash($realSectionExtension->getDisplayName() . ' added.');
                    $this->response->redirect(implode('/', $pathParts));
                    break;

                case 'deletesectionitem':
                	
                	$realSectionItemClass = key($this->getPostCommandData());
                	$deleteId = key($this->getPostCommandData()[$realSectionItemClass]);
                    $sectionVersionId = $data['section']['versionId'];
                	$realSectionClass = $data['section']['realClass'][$sectionVersionId];
                	$realSectionExtension = $sectionVersions->createInstance()->getExtensionByRecord($realSectionClass);
                	$extKey = $realSectionExtension->getExtensionKey();

                	unset($_POST['section']['real'][$deleteId]);

					$syllabus->templateAuthorizationId = $organization ? $organization->templateAuthorizationId : null;
                	list($syllabusVersion, $newSectionVersionId) = $this->saveSection($syllabus, $syllabusVersion, $extKey, $organization);

                	$this->flash('Item deleted from section.', 'info');
                    $pathParts[] = $syllabusVersion->syllabus->id  . '?edit=' . $newSectionVersionId;
                    $pathParts = array_filter($pathParts);
                    $this->response->redirect(implode('/', $pathParts));
                	break;

                case 'deletesection':

                	$sectionVersionId = $data['section']['versionId'];
                	if ($sectionVersions->get($sectionVersionId))
                	{
	                	$realSectionClass = $data['section']['realClass'][$sectionVersionId];
	                	$realSectionExtension = $sectionVersions->createInstance()->getExtensionByRecord($realSectionClass);
	                	$extKey = $realSectionExtension->getExtensionKey();
                		foreach ($_POST['section']['properties'] as $prop => $sections)
                		{
                			unset($sections[$sectionVersionId]);
                		}
                		unset($_POST['real']);

	                	$syllabus->templateAuthorizationId = $organization ? $organization->templateAuthorizationId : null;
                		list($syllabusVersion, $newSectionVersionId) = $this->saveSection(
                			$syllabus, $syllabusVersion, $extKey, $organization);

						$newSectionVersion = $sectionVersions->get($newSectionVersionId);
						$syllabusVersion->sectionVersions->remove($newSectionVersion);
						$syllabusVersion->sectionVersions->save();
						$syllabusVersion->save();
						$syllabusVersion->sectionVersions->save();
						
						$this->flash('The '.$realSectionExtension->getDisplayName().' section has been deleted.', 'primary');
                	}
                	else
                	{
                		$this->flash('Invalid section id.', 'warning');
                	}

                    $pathParts[] = $syllabusVersion->syllabus->id;
                    $pathParts = array_filter($pathParts);
                    $this->response->redirect(implode('/', $pathParts));

                	break;

                case 'savesection':
                case 'savesyllabus':
                    // echo "<pre>"; var_dump($data['section'], $data); die;
                    $syllabus->templateAuthorizationId = $organization ? $organization->templateAuthorizationId : null;
                    list($updated, $syllabusVersion) = $this->saveSyllabus($syllabus);
                    if ($updated)
                    {
                        $this->flash('Syllabus saved.', 'success');
                    }
                    $pathParts[] = $syllabusVersion->syllabus->id;
                    $pathParts = array_filter($pathParts);
                    $this->response->redirect(implode('/', $pathParts));
                    break;
            }
        }

        // ADD SECTION
        if (!$this->request->wasPostedByUser() && ($realSectionName = $this->request->getQueryParameter('add')))
        {
            $realSectionExtension = $sectionVersions->createInstance()->getExtensionByName($realSectionName);
            $canHaveMultiple = true;
            if (!$realSectionExtension->canHaveMultiple())
            {
	            foreach ($syllabusVersion->getSectionVersionsWithExt(true) as $sv)
	            {
	            	if ($sv->extension->getExtensionKey() === $realSectionExtension->getExtensionKey())
	            	{
	            		$canHaveMultiple = false;
	            		break;
	            	}
	            }
            }

            if ($canHaveMultiple && ($realSectionClass = $realSectionExtension->getRecordClass()))
            {
                $realSection = $this->schema($realSectionClass)->createInstance();
                if ($realSectionExtension::getExtensionName() === 'course')
                {
                    $currentCourses = $viewer->classDataUser->getCurrentEnrollments();
                }

                if ($realSectionClass === 'Syllabus_Instructors_Instructors')
                {
                    foreach ($syllabusVersion->sectionVersions as $sv)
                    {
                        if (isset($sv->course_id))
                        {
                            $this->template->defaultInstructor = $viewer;
                            break;
                        }
                    }
                }
                elseif ($realSectionClass === 'Syllabus_Resources_Resources')
                {
			        $campusResources = $this->schema('Syllabus_Syllabus_CampusResource');
                	$this->template->campusResources = $campusResources->find(
			            $campusResources->deleted->isFalse()->orIf($campusResources->deleted->isNull()),
			            ['orderBy' => ['sortOrder', 'title']]
			        );
                }
                if ($realSectionExtension->hasDefaults() && ($defaults = $realSection->getDefaults()))
                {
                	$realSection = $defaults;
                }

                $this->flash('Your section is ready to edit.', 'info');

                $this->template->realSection = $realSection;
                $this->template->realSectionClass = $realSectionClass;
                $this->template->sectionExtension = $realSectionExtension;
                $this->template->editUri = '#section' . $realSectionName . 'Edit';
            }
            else
            {
                $this->flash('Invalid section type.', 'danger');
                $this->response->redirect('syllabus/' . $syllabus->id);
            }
        }

        // EDIT SECTION
        if (!$this->request->wasPostedByUser() && ($sectionVersionId = $this->request->getQueryParameter('edit')))
        {
            $sectionVersion = $sectionVersions->get($sectionVersionId);
            $genericSection = $sectionVersion->section;
            $genericSection->log = $syllabusVersion->sectionVersions->getProperty($sectionVersion, 'log');
            $genericSection->readOnly = (bool)$syllabusVersion->sectionVersions->getProperty($sectionVersion, 'read_only');
            $genericSection->isAnchored = (bool)$syllabusVersion->sectionVersions->getProperty($sectionVersion, 'is_anchored');
            $genericSection->isAnchored = ($genericSection->isAnchored === null) ? true : $genericSection->isAnchored;
            $genericSection->sortOrder = (isset($data['section']['properties']['sortOrder']) ?
                $data['section']['properties']['sortOrder'][$sectionVersion->id] : 
                $syllabusVersion->sectionVersions->getProperty($sectionVersion, 'sort_order')
            );
            $realSection = $sectionVersion->resolveSection();
            $realSectionExtension = $sectionVersion->getExtensionByRecord(get_class($realSection));
            if ($realSectionExtension->getExtensionName() === 'course')
            {
                $currentCourses = $viewer->classDataUser->getCurrentEnrollments();
            }
            elseif (get_class($realSection) === 'Syllabus_Resources_Resources')
            {
		        $campusResources = $this->schema('Syllabus_Syllabus_CampusResource');
            	$this->template->campusResources = $campusResources->find(
		            $campusResources->deleted->isFalse()->orIf($campusResources->deleted->isNull()),
		            ['orderBy' => ['sortOrder', 'title']]
		        );
            }
            $this->template->realSection = $realSection;
            $this->template->realSectionClass = get_class($realSection);
            $this->template->sectionExtension = $realSectionExtension;
            $this->template->genericSection = $genericSection;
            $this->template->currentSectionVersion = $sectionVersion;
            $this->template->isUpstreamSection = $this->isUpstreamSection($sectionVersion, $syllabus, $viewer);
            $this->template->hasDownstreamSection = $this->hasDownstreamSection($sectionVersion, $syllabus, $viewer);
            $this->template->editUri = '#section' . $realSectionExtension->getExtensionName() . 'Edit';
        }


        $siteSettings = $this->getApplication()->siteSettings;
        $userId = $siteSettings->getProperty('university-template-user-id');
        $templateId = $siteSettings->getProperty('university-template-id');
        if (($viewer->id != $userId) && !$this->hasPermission('admin'))
        {
            $this->requireExists($templateId);
        }

        $syllabusSectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
        foreach ($syllabusSectionVersions as $sv)
        {
            $sv->canEditReadOnly = $sv->canEdit($viewer, $syllabusVersion, $organization);
        }

        $this->template->sidebarMinimized = true;
        $this->template->title = $title;
        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $syllabusSectionVersions;
        $this->template->sectionExtensions = $sectionExtensions;
        $this->template->userCourses = $currentCourses ?? null;
        $this->template->organization = $organization;
    }

    protected function saveSection ($syllabus, $syllabusVersion, $extKey, $organization=null)
   	{
        $existingSectionVersionIds = [];
        foreach ($syllabusVersion->sectionVersions as $sv)
        {
            if (isset($sv->$extKey))
            {
                $existingSectionVersionIds[] = $sv->id;
            }
        }

        $syllabus->templateAuthorizationId = $organization ? $organization->templateAuthorizationId : null;
        list($updated, $syllabusVersion) = $this->saveSyllabus($syllabus);
        
        $newSectionVersionId = null;
        foreach ($syllabusVersion->sectionVersions as $sv)
        {
            if (isset($sv->$extKey) && !in_array($sv->id, $existingSectionVersionIds))
            {
                $newSectionVersionId = $sv->id;
                break;
            }
        }

        return [$syllabusVersion, $newSectionVersionId];
   	}

    public function view ()
    {
        $viewer = $this->requireLogin();
        
        $this->setSyllabusTemplate();

        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        $sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id', ['allowNew' => true]);
        $syllabusVersion = $syllabusVersions->get($this->request->getQueryParameter('v')) ?? $syllabus->latestVersion;

        // TODO: make sure this viewer has permission to view

        $title = ($syllabus->inDatasource ? 'Edit' : 'Create') . ' Syllabus';
        $this->setPageTitle($title);

        $routeBase = $this->getRouteVariable('routeBase', '');
        $organization = $this->getRouteVariable('organization', null);

        $pathParts = [];
        $pathParts[] = $this->getRouteVariable('routeBase');
        $pathParts[] = 'syllabus';

        $this->template->addBreadcrumb('syllabi', 'My Syllabi');
        $this->template->addBreadcrumb('syllabus/'.$syllabus->id, 'Edit');
        $this->template->addBreadcrumb('syllabus/'.$syllabus->id.'/view', $syllabusVersion->title);
        $this->template->title = $title;
        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
        $this->template->organization = $organization;
    }

    /**
     * Bump syllabus and section versions--creating new instances of generic, real, and subsections.
     */  
    protected function saveSyllabus ($syllabus, $paramData=null)
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
                if (!$oldSyllabusVersion->sectionVersions->has($prevSectionVersion))
                {
                    $this->accessDenied('Incorrect section version id.');
                }

                if ($this->isInheritedSection($prevSectionVersion, $syllabus->templateAuthorizationId) ||
                    $this->isUpstreamSection($prevSectionVersion, $syllabus, $viewer))
                {
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
            $errorMsg = $realSection->processEdit($this->request);
            if ($errorMsg && $errorMsg !== '')
            {
            	$this->template->addUserMessage($errorMsg, '');
            }
            if (isset($data['section']) && isset($data['section']['real']) && isset($data['section']['real']['external_key']))
            {	// TODO: figure out why this isn't working in the Courses procesEdit()
                $realSection->absorbData($data['section']['real']);
                $realSection->externalKey = $data['section']['real']['external_key'];
            }
            $realSection->save();

            if ($extKey === 'course_id' && $realSection->externalKey)
            {
                $courses = $this->schema('Syllabus_ClassData_CourseSection');
                $course = $courses->findOne($courses->id->equals($realSection->externalKey));
                $course->syllabus_id = $syllabus->id ?? '';
                $course->save();
                $courseData = $course->getData();
                unset($courseData['id']);
                $realSection->absorbData($courseData);
                $realSection->save();
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
	            $newSyllabusVersion->syllabus_id = $syllabus->id;            	
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
            $inherited = false;
            // $oldCount = isset($oldSyllabusVersion->sectionVersions) ? count($oldSyllabusVersion->sectionVersions) : 0;
            // $newCount = count($newSyllabusVersion->sectionVersions);

            // if editing a section, remove it's previous version from the new syllabus version
            if ($prevSectionVersion)
            {
                $removeSectionVersion = $prevSectionVersion;
                foreach ($newSyllabusVersion->sectionVersions as $sectionVersion)
                {
                    if ($sectionVersion->sectionId === $removeSectionVersion->sectionId)
                    {
                        $removeSectionVersion = $sectionVersion;
                        break;
                    }
                }
            	$newSyllabusVersion->sectionVersions->remove($removeSectionVersion);
            }
            $newSyllabusVersion->sectionVersions->save();
            $newSyllabusVersion->save();

            // TODO: determine if this is an inherited section that is already readOnly??
            if (!isset($data['section']['properties']['readOnly']) && $inherited)
            {
                $data['section']['properties']['readOnly'][$sectionVersionId] = true;
            }

            // add new section version to this new syllabus version
            // pad sort order with a leading zero so always has two digits
            $defaultPosition = $newSyllabusVersion->sectionCount + 1;
            $sortOrder = $defaultPosition;
            if (isset($data['section']['properties']['sortOrder']))
            {
            	$sortOrder = $data['section']['properties']['sortOrder'][$sectionVersionId];
            	$sortOrder = strlen($sortOrder) === 2 ? $sortOrder : '0'.$sortOrder;
            }
            $newSyllabusVersion->sectionVersions->add($newSectionVersion);
            $newSyllabusVersion->sectionVersions->setProperty($newSectionVersion, 'sort_order', $sortOrder);
            $newSyllabusVersion->sectionVersions->setProperty($newSectionVersion, 'read_only', 
            	isset($data['section']['properties']['readOnly'][$sectionVersionId])
            );
            $newSyllabusVersion->sectionVersions->setProperty($newSectionVersion, 'is_anchored', 
            	isset($data['section']['properties']['isAnchored']) ? 
                    $data['section']['properties']['isAnchored'][$sectionVersionId] : 
                    false
            );
            $newSyllabusVersion->sectionVersions->setProperty($newSectionVersion, 'log', 
            	isset($data['section']['properties']['log']) ? 
                    $data['section']['properties']['log'][$sectionVersionId] : 
                    ''
            );
            $newSyllabusVersion->sectionVersions->setProperty($newSectionVersion, 'inherited', $inherited);
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
	            	$sortOrder = $data['section']['properties']['sortOrder'][$sv->id];
	            	$sortOrder = strlen($sortOrder) === 2 ? $sortOrder : '0'.$sortOrder;
                    $newSyllabusVersion->sectionVersions->setProperty($sv, 'sort_order', $sortOrder);
                }
            }
            $newSyllabusVersion->save();
            $newSyllabusVersion->sectionVersions->save();
        }

        return [$anyChange, $newSyllabusVersion];
    }

    private function createCourseSyllabus ($versionId, $fromCourseSection, $inherited=false)
    {
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $syllabus = ($versionId === 'new') ? $syllabi->createInstance() : $syllabi->get($versionId);

        if ($syllabus)
        {
            $data = [
                'syllabus' => [
                    'title' => ('Syllabus for ' . $fromCourseSection->title . ' (' . $fromCourseSection->term . ')'),
                    'description' => ($fromCourseSection->getShortName() . ' course syllabus'),
                ],
                'section' => [
                    'versionId' => 'new',
                    'realClass' => ['new' => 'Syllabus_Courses_Course'],
                    'extKey' => ['new' => 'course_id'],
                    'properties' => [
                        'sortOrder' => ['new' => '01'],
                        'isAnchored' => ['new' => true],
                        'readOnly' => ['new' => false],
                        'inherited' => ['new' => $inherited],
                        'log' => ['new' => ''],
                    ],
                    'real' => [
                        'external_key' => $fromCourseSection->id,
                        'title' => $fromCourseSection->title,
                        'description' => $fromCourseSection->description,
                        'sectionNumber' => $fromCourseSection->sectionNumber,
                        'classNumber' => $fromCourseSection->classNumber,
                        'semester' => $fromCourseSection->getSemester(true),
                        'year' => $fromCourseSection->year,
                    ],
                    'generic' => [
                        'new' => [
                            'title' => 'Course Information',
                            'description' => '',
                        ],
                    ],
                ],
            ];

            return $this->saveSyllabus($syllabus, $data);
        }
    }

    // Use this for when cloning one course syllabus to another and need to replace
    // the previous Course Information section
    private function updateCourseSyllabus ($fromSyllabus, $toSyllabus, $cdCourseSection)
    {
        $syllabusVersion = $toSyllabus->latestVersion;
        $syllabusVersion->title = ('Syllabus for ' . $cdCourseSection->title . ' (' . $cdCourseSection->term . ')');
        $syllabusVersion->description = ($cdCourseSection->getShortName() . ' course syllabus');

        $pastSectionVersion = null;
        foreach ($fromSyllabus->latestVersion->getSectionVersionsWithExt() as $sv)
        {
            if (isset($sv->course_id))
            {
                $pastSectionVersion = $sv;
                $syllabusVersion->sectionVersions->remove($pastSectionVersion);
                break;
            }
        }

        $genericSection = $this->schema('Syllabus_Syllabus_Section')->createInstance();
        $genericSection->createdById = $toSyllabus->createdById;
        $genericSection->createdDate = new DateTime;
        $genericSection->save();

        if ($pastSectionVersion)
        {
            $pastSvTitle = $pastSectionVersion->extension->getDisplayName();
            $recordClass = $pastSectionVersion->extension->getRecordClass();
        }
        else
        {
            $pastSvTitle = '';
            $recordClass = 'Syllabus_Courses_Course';
        }
        $realSection = $this->schema($recordClass)->createInstance();
        $realSection->externalKey = $cdCourseSection->id;
        $realSection->title = $cdCourseSection->title;
        $realSection->description = $cdCourseSection->description;
        $realSection->sectionNumber = $cdCourseSection->sectionNumber;
        $realSection->classNumber = $cdCourseSection->classNumber;
        $realSection->semester = $cdCourseSection->getSemester(true);
        $realSection->year = $cdCourseSection->year;
        $realSection->save();
        
        $cdCourseSection->syllabus_id = $toSyllabus->id;
        $cdCourseSection->save();

        $sectionVersion = $this->schema('Syllabus_Syllabus_SectionVersion')->createInstance();
        $sectionVersion->createdDate = new DateTime;
        $sectionVersion->sectionId = $genericSection->id;
        $sectionVersion->course_id = $realSection->id;
        $sectionVersion->title = $pastSvTitle;
        $sectionVersion->save();

        $syllabusVersion->sectionVersions->add($sectionVersion);
        $syllabusVersion->sectionVersions->setProperty($sectionVersion, 'sort_order', '01');
        $syllabusVersion->sectionVersions->setProperty($sectionVersion, 'inherited', false);
        $syllabusVersion->sectionVersions->setProperty($sectionVersion, 'read_only', false);
        $syllabusVersion->sectionVersions->setProperty($sectionVersion, 'is_anchored', true);
        $syllabusVersion->sectionVersions->setProperty($sectionVersion, 'log', 
            'Syllabus cloned from previous course syllabus.'
        );

        $syllabusVersion->sectionVersions->save();
        $syllabusVersion->save();

        return $syllabusVersion;
    }

    private function hasCloningPermission ($syllabus, $user=null)
    {
        $user = $user ?? $this->requireLogin();
        $hasPermission = true;

        if ($syllabus->templateAuthorizationId)
        {
            $organization = null;
            $hasPermission = false;
            list($type, $id) = explode('/', $syllabus->templateAuthorizationId);
            
            switch ($type)
            {
                case 'departments':
                    $organization = $this->schema('Syllabus_AcademicOrganizations_Department')->get($id);

                    break;
                case 'colleges':
                    echo "<pre>"; var_dump('need to implement'); die;
                    break;
                default:
                    break;
            }

            if (!$organization)
            {
                $this->accessDenied("Could not find any '{$type}' with id '{$id}'.");            
            }

            $hasPermission = $organization->userHasRole($user, 'member');
        }
        elseif ($syllabus->createdById !== $user->id)
        {
            $hasPermission = false;
        }

        return $hasPermission;
    }

    public function isInheritedSection ($sectionVersion, $templateAuthorizationId)
    {
        $result = false;
        if ($sectionVersion)
        {
            // sorry, not sorry
            $rs = pg_query("
            select count(distinct s.id) from syllabus_syllabus_versions sv 
            inner join syllabus_syllabus_version_section_version_map svsv 
                on (sv.id = svsv.syllabus_version_id)
            inner join syllabus_section_versions sec
                on (sec.id = svsv.section_version_id)
            inner join syllabus_section_versions sec2
                on (sec2.section_id = sec.section_id)
            inner join syllabus_syllabus_version_section_version_map svsv2
                on (sec2.id = svsv2.section_version_id)
            inner join syllabus_syllabus_versions sv2
                on (sv2.id = svsv2.syllabus_version_id)
            inner join syllabus_syllabus s 
                on ((s.id = sv.syllabus_id) or (s.id = sv2.syllabus_id))
            where 
                svsv.section_version_id = {$sectionVersion->id} and 
                s.template_authorization_id != '{$templateAuthorizationId}' and
                svsv.inherited = 'f'");

            while (($row = pg_fetch_row($rs)))
            {
                $result = $row[0];
                break;
            }          
        }

        return $result;
    }

    public function isUpstreamSection ($sectionVersion, $syllabus, $account)
    {
        $result = false;
        if ($sectionVersion)
        {
            $rs = pg_query("
            select count(distinct s.id) from syllabus_syllabus_versions sv 
            inner join syllabus_syllabus_version_section_version_map svsv 
                on (sv.id = svsv.syllabus_version_id)
            inner join syllabus_section_versions sec
                on (sec.id = svsv.section_version_id)
            inner join syllabus_section_versions sec2
                on (sec2.section_id = sec.section_id)
            inner join syllabus_syllabus_version_section_version_map svsv2
                on (sec2.id = svsv2.section_version_id)
            inner join syllabus_syllabus_versions sv2
                on (sv2.id = svsv2.syllabus_version_id)
            inner join syllabus_syllabus s 
                on ((s.id = sv.syllabus_id) or (s.id = sv2.syllabus_id))
            where 
                svsv.section_version_id = {$sectionVersion->id} and 
                s.id != {$syllabus->id} and
                s.created_by_id = {$account->id} and
                s.created_by_id is not null and 
                svsv.inherited = 'f' and svsv2.inherited = 'f'");

            while (($row = pg_fetch_row($rs)))
            {
                $result = $row[0];
                break;
            }          
        }

        return $result;
    }

    public function hasDownstreamSection ($sectionVersion, $syllabus, $account)
    {
        $result = false;
        if ($sectionVersion)
        {
            $rs = pg_query("
            select count(distinct s.id) from syllabus_syllabus_versions sv 
            inner join syllabus_syllabus_version_section_version_map svsv 
                on (sv.id = svsv.syllabus_version_id)
            inner join syllabus_section_versions sec
                on (sec.id = svsv.section_version_id)
            inner join syllabus_section_versions sec2
                on (sec2.section_id = sec.section_id)
            inner join syllabus_syllabus_version_section_version_map svsv2
                on (sec2.id = svsv2.section_version_id)
            inner join syllabus_syllabus_versions sv2
                on (sv2.id = svsv2.syllabus_version_id)
            inner join syllabus_syllabus s 
                on ((s.id = sv.syllabus_id) or (s.id = sv2.syllabus_id))
            where 
                svsv.section_version_id = {$sectionVersion->id} and 
                s.id != {$syllabus->id} and
                s.created_by_id = {$account->id} and
                s.created_by_id is not null and 
                svsv.inherited = 't' and svsv2.inherited = 't'");

            while (($row = pg_fetch_row($rs)))
            {
                $result = $row[0];
                break;
            }          
        }

        return $result;
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

    // TODO: put this in ActiveRecord?
    // TODO: update for multiple syllabusIds requests at a time
    private function getScreenshotUrl ($syllabusId, $screenshotter=null, $cacheImages=true)
    {
        $viewer = $this->requireLogin();
        $syllabus = $this->requireExists($this->schema('Syllabus_Syllabus_Syllabus')->get($syllabusId));
        $urls = [];
        $messages = [];
        $uid = $viewer->id;

        $keyPrefix = "{$uid}-";
        $screenshotter = $screenshotter ?? new Syllabus_Services_Screenshotter($this->getApplication());
        $screenshotter->saveUids($uid, $syllabus->id);

        $urls[$syllabus->id] = $this->baseUrl("syllabus/{$syllabus->id}/screenshot");
        $results = $screenshotter->concurrentRequests($urls, $cacheImages, $keyPrefix);
        $results = json_decode($results);

        return $results;
    }

    public function screenshot ()
    {
        // $viewer = $this->requireLogin();

        $this->setScreenshotTemplate();

        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id');
        $syllabusVersion = $syllabusVersions->get($this->request->getQueryParameter('v')) ?? $syllabus->latestVersion;


        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
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


}