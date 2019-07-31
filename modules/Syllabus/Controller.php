<?php

/**
 * Handles main business logic for entity/user syllabi.
 * 
 * @author      Steve Pedersen <pedersen@sfsu.edu>
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Syllabus_Controller extends Syllabus_Master_Controller {

    private $_keyPrefix;

    public static function getRouteMap ()
    {
        return [
            '/'                         => ['callback' => 'mySyllabi'],
            'syllabi'                   => ['callback' => 'mySyllabi'],
            'syllabus/:id'              => ['callback' => 'edit', ':id' => '[0-9]+|new'],
            'syllabus/:id/view'         => ['callback' => 'view', ':id' => '[0-9]+'],
            'syllabus/:id/share'        => ['callback' => 'share', ':id' => '[0-9]+'],
            'syllabus/:id/delete'       => ['callback' => 'delete', ':id' => '[0-9]+'],
            'syllabus/:id/print'        => ['callback' => 'print', ':id' => '[0-9]+'],
            'syllabus/:id/word'         => ['callback' => 'word', ':id' => '[0-9]+'],
            'syllabus/:id/export'       => ['callback' => 'export', ':id' => '[0-9]+'],
            'syllabus/:id/screenshot'   => ['callback' => 'screenshot', ':id' => '[0-9]+'],
            'syllabus/:id/ilearn'       => ['callback' => 'iLearn', ':id' => '[0-9]+'],
            'syllabus/courses'          => ['callback' => 'courseLookup'],
            'syllabus/start'            => ['callback' => 'start'],
            'syllabus/startwith/:id'    => ['callback' => 'startWith', ':id' => '[0-9]+'],
            'syllabus/migrate'          => ['callback' => 'migrate'],
        ];
    }

    public function mySyllabi ()
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $courseSections = $this->schema('Syllabus_ClassData_CourseSection');
        $offset = 0;
        $limit = 9;

        $siteSettings = $this->getApplication()->siteSettings;
        $userId = $siteSettings->getProperty('university-template-user-id');
        $templateId = $siteSettings->getProperty('university-template-id');
        if (($viewer->id != $userId) && !$this->hasPermission('admin'))
        {
            $this->requireExists($templateId);
        }
        $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
        
        $roles = $this->schema('Syllabus_AuthN_Role');
        $studentRole = $roles->findOne($roles->name->equals('Student'));
        $isStudent = false;
        if ($viewer->roles->has($studentRole))
        {
            $isStudent = true;
        }

        switch ($mode = $this->request->getQueryParameter('mode', 'overview')) {

            case 'courses':
                if ($isStudent)
                {
                    $this->response->redirect('syllabi');
                }
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
                $roles = $this->schema('Syllabus_AuthN_Role');
                $studentRole = $roles->findOne($roles->name->equals('Student'));
                $isStudent = false;

                if ($viewer->roles->has($studentRole))
                {
                    $isStudent = true;

                    $myCourses = $viewer->classDataUser->getCurrentEnrollments();
                    $courses = [];
                    foreach ($myCourses as $i => $courseSection)
                    {
                        $index = $i % 5;
                        $courseSyllabus = $syllabi->get($courseSection->syllabus_id);
                        $courseSection->courseSyllabus = $courseSyllabus;
                        $courseSection->createNew = $courseSyllabus ? false : true;
                        $courses[$courseSection->term][] = $courseSection;
                        $imageUrl = '';
                        if ($courseSyllabus)
                        {
                            $sid = $courseSyllabus->id;
                            $results = $this->getScreenshotUrl($sid, $screenshotter);
                            $imageUrl = $results->imageUrls->$sid;
                        }
                        $courseSection->imageUrl = $imageUrl;
                    }

                    $this->template->allCourses = $courses;

                }
                else
                {
                    $campusResources = $this->schema('Syllabus_Syllabus_CampusResource');
                    
                    if (!$this->hasPermission('admin'))
                    {
                        $userSyllabi = $syllabi->find(
                            $syllabi->createdById->equals($viewer->id)->andIf($syllabi->templateAuthorizationId->isNull()), 
                            ['orderBy' => ['-modifiedDate', '-createdDate'], 'limit' => $limit, 'offset' => $offset]
                        );                    
                    }
                    else
                    {
                        $userSyllabi = $syllabi->find(
                            $syllabi->createdById->equals($viewer->id), 
                            ['orderBy' => ['-modifiedDate', '-createdDate'], 'limit' => $limit, 'offset' => $offset]
                        );
                    }
                  
                    foreach ($userSyllabi as $userSyllabus)
                    {
                        $sid = $userSyllabus->id;
                        $results = $this->getScreenshotUrl($sid, $screenshotter);
                        $userSyllabus->imageUrl = $results->imageUrls->$sid;
                        $userSyllabus->hasCourseSection = false;
                        foreach ($userSyllabus->latestVersion->getSectionVersionsWithExt(true) as $sv)
                        {
                            if (isset($sv->extension) && $sv->extension->getExtensionKey() === 'course_id' && isset($sv->resolveSection()->externalKey))
                            {
                                $userSyllabus->hasCourseSection = true;
                                break;
                            }                        
                        }
                    }
                    $this->template->campusResources = $campusResources->find(
                        $campusResources->deleted->isFalse()->orIf($campusResources->deleted->isNull()),
                        ['orderBy' => ['sortOrder', 'title']]
                    );   

                    $this->template->syllabi = $userSyllabi;        
                }

                $this->template->isStudent = $isStudent;
                

                break;
        }

        if ($this->request->wasPostedByUser())
        {    
            $data = $this->request->getPostParameters();

            switch ($this->getPostCommand()) 
            {
                
                case 'resourceToSyllabi':
                    $resourceId = key($this->getPostCommandData());
                    $this->template->showSaveResourceModal = true;
                    
                    $addMessage = 'No syllabi were selected';
                    if (isset($data['syllabi']))
                    {
                        $results = $this->saveResourceToSyllabi($resourceId, $data['syllabi']);
                        $addSuccess = isset($results['status']) && ($results['status'] === 'success');
                        $addMessage = $results['message'];
                    }

                    $this->template->addSuccess = $addSuccess;
                    $this->template->addMessage = $addMessage;
                    break;

                case 'courseNew':
                    $courseSection = $courseSections->get(key($this->getPostCommandData()));
                    $universityTemplate = $this->requireExists($syllabi->get($templateId));
                    $syllabus = $this->startWith($universityTemplate, true, true);
                    list($success, $newSyllabusVersion) = $this->createCourseSyllabus($syllabus->id, $courseSection);
                    if ($success)
                    {
                        $this->flash('Your new course syllabus is ready for you to edit and add more sections.', 'success');
                        $this->response->redirect('syllabus/' . $newSyllabusVersion->syllabus->id);
                    }
                    break;

                case 'courseClone':
                    $courseSection = $courseSections->get(key($this->getPostCommandData()));
                    $sid = array_shift($data['course']);
                    $pastSyllabus = isset($sid['syllabusId']) ? $syllabi->get($sid['syllabusId']) : null;
  
                    if ($pastSyllabus && $this->hasSyllabusPermission($pastSyllabus, $viewer, 'clone'))
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
        $pStartFromNothing = false;
        if (($viewer->id != $userId) && !$this->hasPermission('admin'))
        {
            $this->requireExists($templateId);
        }
        elseif (!$templateId && $this->hasPermission('admin'))
        {
            $pStartFromNothing = true;
        }

        $roles = $this->schema('Syllabus_AuthN_Role');
        $studentRole = $roles->findOne($roles->name->equals('Student'));
        $isStudent = false;
        if ($viewer->roles->has($studentRole))
        {
            $this->accessDenied('nope');
        }

        $templatesAvailable = false;
        $pastCourseSyllabi = null;
        $courseSection = null;

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
            $templatesAvailable = $templatesAvailable || !empty($userSyllabi);
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
                $templatesAvailable = $templatesAvailable || !empty($templates[$cs->department->id]);
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

        if (!$templatesAvailable && !$pastCourseSyllabi && !$pStartFromNothing)
        {
            $startingTemplate = $this->requireExists($syllabi->get($templateId));
            $syllabus = $this->startWith($startingTemplate, true, true);
            $version = $syllabus->latestVersion;
            if ($courseSection)
            {
                list($success, $version) = $this->createCourseSyllabus($syllabus->id, $courseSection);
                $this->flash("
                    Your new syllabus draft includes all SF State requirements and course information for 
                    $courseSection->fullDisplayName. It is ready for you to edit.", 
                    'success'
                );
            }
            else
            {
                $this->flash("
                    Your new syllabus draft includes all SF State requirements. It is ready for you to edit.", 
                    'success'
                );
            }
            $pathParts[] = $version->syllabus->id;
            $pathParts = array_filter($pathParts);
            $this->response->redirect(implode('/', $pathParts));
        }

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

        $this->template->pStartFromNothing = $pStartFromNothing;
    }

    public function startWith ($fromSyllabus=null, $return=false, $baseTemplate=false)
    {
        $viewer = $this->requireLogin();
        if (!$fromSyllabus)
        {
            $fromSyllabus = $this->requireExists($this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id'));
        }
        
        if (!$baseTemplate && !$this->hasSyllabusPermission($fromSyllabus, $viewer, 'clone'))
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
        $toSyllabusVersion->title = $toSyllabusVersion->title . ' (Copy)';
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
        if (!$this->hasPermission('admin') && !$this->hasSyllabusPermission($syllabus, $viewer, 'edit'))
        {
            $this->accessDenied("You do not have edit access for this syllabus.");
        }

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
        if ((!$templateId && $this->hasPermission('admin')) || ($templateId && $templateId == $syllabus->id))
        {
            $this->template->isUniversityTemplate = true;
        }
        elseif (!$templateId && $this->hasPermission('admin'))
        {
            $this->template->isDetachedSyllabus = true;
        }


        if ($this->request->wasPostedByUser())
        {      
            switch ($this->getPostCommand()) 
            {
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

                    if (!$syllabus->templateAuthorizationId) 
                    {
                        $syllabus->templateAuthorizationId = $organization ? $organization->templateAuthorizationId : null;    
                    }
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

                        if (!$syllabus->templateAuthorizationId) 
                        {
                            $syllabus->templateAuthorizationId = $organization ? $organization->templateAuthorizationId : null;    
                        }
	                	
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
                    if (!$syllabus->templateAuthorizationId)
                    {
                        $syllabus->templateAuthorizationId = $organization ? $organization->templateAuthorizationId : null;
                    }
                    
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
            if ($realSectionName === 'learning_outcomes')
            {
                $this->flash('The Student Learning Outcomes section type is unavailable at this time.', 'danger');
                $this->response->redirect('syllabus/' . $syllabus->id);
            }
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

            $upstreamSyllabi = $this->getUpstreamSyllabi($sectionVersion, $syllabus, $viewer);
            if ($upstreamSyllabi)
            {
                $schema = $this->schema('Syllabus_Syllabus_Syllabus');
                $upstreamSyllabi = $schema->find($schema->id->inList($upstreamSyllabi));
            }
            $this->template->realSection = $realSection;
            $this->template->realSectionClass = get_class($realSection);
            $this->template->sectionExtension = $realSectionExtension;
            $this->template->genericSection = $genericSection;
            $this->template->currentSectionVersion = $sectionVersion;
            $this->template->upstreamSyllabi = $upstreamSyllabi;
            // $this->template->isUpstreamSection = $this->isUpstreamSection($sectionVersion, $syllabus, $viewer);
            $this->template->hasDownstreamSection = $this->hasDownstreamSection($sectionVersion, $syllabus, $viewer) &&
                !$this->isInheritedSection($sectionVersion, $syllabus->templateAuthorizationId);
            $this->template->editUri = '#section' . $realSectionExtension->getExtensionName() . 'Edit';
        }


        $siteSettings = $this->getApplication()->siteSettings;
        $userId = $siteSettings->getProperty('university-template-user-id');
        $templateId = $siteSettings->getProperty('university-template-id');
        if (($viewer->id != $userId) && !$this->hasPermission('admin'))
        {
            $this->requireExists($templateId);
        }

        $hasCourseSection = false;
        $syllabusSectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
        foreach ($syllabusSectionVersions as $sv)
        {

            $sv->canEditReadOnly = $sv->canEdit($viewer, $syllabusVersion, $organization);
            if ($sv->extension->getExtensionKey() === 'course_id' && isset($sv->resolveSection()->externalKey))
            {
            	$hasCourseSection = true;
            }
        }

        $this->template->sidebarMinimized = true;
        $this->template->hasCourseSection = $hasCourseSection;
        $this->template->title = $title;
        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $syllabusSectionVersions;
        $this->template->sectionExtensions = $sectionExtensions;
        $this->template->userCourses = $currentCourses ?? null;
        $this->template->organization = $organization;
        $this->template->routeBase = $routeBase;
    }

    public function share ()
    {
        $viewer = $this->requireLogin();  
        $syllabus = $this->requireExists($this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id'));
        $syllabusVersion = $syllabus->latestVersion;
        $publishSchema = $this->schema('Syllabus_Syllabus_PublishedSyllabus');
        $published = $this->getPublishedSyllabus($syllabus);

        if (!$this->hasPermission('admin') && !$this->hasSyllabusPermission($syllabus, $viewer))
        {
            $this->accessDenied("You do not have share access for this syllabus.");
        }

        $this->setPageTitle('Share Syllabus');
        $this->addBreadcrumb('syllabus/'.$syllabus->id, 'Edit Syllabus');
        $this->addBreadcrumb('syllabus/'.$syllabus->id.'/share', 'Share');

        $sid = $syllabus->id;
        $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
        $results = $this->getScreenshotUrl($sid, $screenshotter, true);
        $syllabus->imageUrl = $results->imageUrls->$sid;

        if ($this->request->wasPostedByUser())
        {   
            $shareLevel = $this->request->getPostParameter('share');
            $published = $this->publishSyllabus($syllabus, $shareLevel, $published);

            $this->flash('Share level updated!', 'success');
            $this->response->redirect('syllabus/' . $syllabus->id . '/share');
        }

        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->courseInfoSection = $syllabusVersion->getCourseInfoSection();
        $this->template->published = $published;
        $this->template->shareLevel = $this->getShareLevel($syllabus);
    }

    public function getPublishedSyllabus ($syllabus)
    {
        $schema = $this->schema('Syllabus_Syllabus_PublishedSyllabus');
        $published = $schema->findOne(
            $schema->syllabusId->isNotNull()->andIf(
                $schema->syllabusId->equals($syllabus->id)
            )
        );
        return $published;        
    }

    public function getShareLevel ($syllabus)
    {
        $published = $this->getPublishedSyllabus($syllabus);
        return ($published ? $published->shareLevel : 'private');
    }

    private function publishSyllabus ($syllabus, $shareLevel='all', $published=null)
    {
        $schema = $this->schema('Syllabus_Syllabus_PublishedSyllabus');
        $publishedSyllabus = $published ? $schema->get($published->id) : $schema->createInstance();
        $publishedSyllabus->syllabusId = $syllabus->id;
        $publishedSyllabus->shareLevel = $shareLevel;
        $publishedSyllabus->publishedDate = new DateTime;
        $publishedSyllabus->save();

        return $publishedSyllabus;
    }

    public function delete ()
    {
        $viewer = $this->requireLogin();  
        $syllabus = $this->requireExists($this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id'));
        $syllabusVersion = $syllabus->latestVersion;

        if (!$this->hasPermission('admin') && !$this->hasSyllabusPermission($syllabus, $viewer, 'delete'))
        {
            $this->accessDenied("You don't have permission to delete this syllabus.");
        }

        $title = 'Delete Syllabus?';
        $this->setPageTitle($title);

        $routeBase = $this->getRouteVariable('routeBase', '');
        $organization = $this->getRouteVariable('organization', null);

        $this->setPageTitle('Share Syllabus');
        $this->addBreadcrumb($routeBase . 'syllabus/'.$syllabus->id, 'Edit Syllabus');
        $this->addBreadcrumb($routeBase . 'syllabus/'.$syllabus->id.'/delete', 'Delete');

        $routeBase = $routeBase === '' ? 'syllabi' : $routeBase;
        $pathParts = [];
        $pathParts[] = $routeBase;
        $pathParts[] = 'syllabus';

        $sid = $syllabus->id;
        $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
        $results = $this->getScreenshotUrl($sid, $screenshotter, true);
        $syllabus->imageUrl = $results->imageUrls->$sid;

        $hasDownstreamSyllabiSection = false;
        foreach ($syllabusVersion->sectionVersions as $sv)
        {
            if ($this->hasDownstreamSection($sv, $syllabus, $viewer))
            {
                $hasDownstreamSyllabiSection = true;
                break;
            }
        }

        if ($this->request->wasPostedByUser())
        {   
            switch ($this->getPostCommand()) 
            {
                case 'deletesyllabus':

                    if ($this->hasPermission('admin') || $this->hasSyllabusPermission($syllabus, $viewer, 'delete'))
                    {
                        $schema = $this->schema('Syllabus_ClassData_CourseSection');
                        $courseSections = $schema->find(
                            $schema->syllabus_id->equals($syllabus->id)
                        );
                        if ($courseSections)
                        {
                            foreach ($courseSections as $courseSection)
                            {
                                $courseSection->syllabusId = null;
                                $courseSection->save();
                            }
                        }
                        $syllabus->delete();
                        $this->flash('Delete successful', 'success');
                    }
                    else
                    {
                        $this->flash('You do not have permission to delete this.', 'danger');
                    }
                    $this->response->redirect($routeBase);
                    break;
            }
        }

        $this->template->title = $title;
        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->hasDownstreamSyllabiSection = $hasDownstreamSyllabiSection;
        $this->template->organization = $organization;
        $this->template->routeBase = $routeBase;
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

        if (!$syllabus->templateAuthorizationId)
        {
            $syllabus->templateAuthorizationId = $organization ? $organization->templateAuthorizationId : null;
        }
        
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

    public function print ()
    {
        $viewer = $this->requireLogin();
        $syllabus = $this->requireExists($this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id'));
        $syllabusVersion = $syllabus->latestVersion;

        if (!$this->hasPermission('admin') && !$this->hasSyllabusPermission($syllabus, $viewer))
        {
            $this->accessDenied("You do not have edit access for this syllabus.");
        }
        $this->setPrintTemplate();
        $this->setPageTitle('Print Syllabus');
        $this->template->addBreadcrumb('syllabi', 'My Syllabi');

        list($type, $courseSection) = $this->getEnrollmentType($syllabus, $viewer);

        if ($type === 'student' && $courseSection)
        {
            $this->template->addBreadcrumb('syllabus/'.$syllabus->id.'/view', $courseSection->title);
        }
        else
        {
            $this->template->addBreadcrumb('syllabus/'.$syllabus->id.'/view', $syllabusVersion->title);    
        }

        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
    }

    public function export ()
    {
        // $viewer = $this->requireLogin();
        $syllabus = $this->requireExists($this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id'));
        $syllabusVersion = $syllabus->latestVersion;

        $this->setExportTemplate();
        $this->setPageTitle('Export Syllabus');

        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
    }

    public function word ()
    {
        $viewer = $this->requireLogin();
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id');
        $url = $this->getApplication()->siteSettings->getProperty('atoffice-api-url');
        
        if (!$this->hasSyllabusPermission($syllabus, $viewer, 'view'))
        {
            $this->accessDenied('Nope');
        }

        $courseSection = null;
        if ($sectionVersion = $syllabus->latestVersion->getCourseInfoSection())
        {
            $courseSection = $sectionVersion->resolveSection()->classDataCourseSection;
        }
        if ($courseSection)
        {
            $title = 'Syllabus for ' . $courseSection->getShortName(true);
        }
        else
        {
            $title = $syllabus->latestVersion->title;
        }
        $url = $url ?? 'https://atoffice.test.at.sfsu.edu/api/session';
        $data = [
            'title' => $title,
            'source' => $this->baseUrl('syllabus/'.$syllabus->id.'/export'),
            'sessionType' => 'word'
        ];

        list($code, $response) = $this->sendRequest($url, true, $data);

        if ($code === 200)
        {
            $response = json_decode($response, true);
            if (isset($response['data']) && isset($response['data']['url']))
            {
                $url = $response['data']['url'];
                $filename = $title . '.doc';
                header('Content-Type: application/octet-stream');
                header("Content-Transfer-Encoding: Binary"); 
                header("Content-disposition: attachment; filename=\"".$filename."\""); 
                readfile($url);
                exit;
            }
        }
        else
        {
            $this->flash(
                'The Word exporter is currently unavailble. Please contact support for more information',
                'warning'
            );
            $this->response->redirect('syllabus/'.$syllabus->id.'/view');
        }

        exit;
    }

    protected function sendRequest ($url, $post=false, $postData=[])
    {
        $data = null;
        
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($post) 
        { 
            curl_setopt($ch, CURLOPT_POST, true); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
        } 
        $rawData = curl_exec($ch);

        if (!curl_error($ch)) {
            // $data = json_decode($rawData, true);
            $data = $rawData;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        
        return [$httpCode, $data];
    }

    public function view ()
    {
        $viewer = $this->requireLogin();
        
        $this->setSyllabusTemplate();

        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        $sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id', ['allowNew' => false]);
        $syllabusVersion = $syllabusVersions->get($this->request->getQueryParameter('v')) ?? $syllabus->latestVersion;

        if (!$this->hasSyllabusPermission($syllabus, $viewer, 'view'))
        {
            $this->accessDenied('Nope');
        }

        $editable = false;
        if (($syllabus->createdById === $viewer->id) || $this->hasPermission('admin'))
        {
            $editable = true;
        }

        $title = ($syllabus->inDatasource ? 'Edit' : 'Create') . ' Syllabus';
        $this->setPageTitle($title);

        $routeBase = $this->getRouteVariable('routeBase', '');
        $organization = $this->getRouteVariable('organization', null);
        if ($organization)
        {
            $this->template->addBreadcrumb($routeBase, $organization->name . ' Home');
        }
        else
        {
            $this->template->addBreadcrumb('syllabi', 'My Syllabi');
        }

        $pathParts = [];
        $pathParts[] = $this->getRouteVariable('routeBase');
        $pathParts[] = 'syllabus';

        list($type, $courseSection) = $this->getEnrollmentType($syllabus, $viewer);

        if ($type === 'student' && $courseSection)
        {
            $this->template->addBreadcrumb('syllabus/'.$syllabus->id.'/view', $courseSection->title);
        }
        else
        {
            $this->template->addBreadcrumb('syllabus/'.$syllabus->id.'/view', $syllabusVersion->title);    
        }
        
        $this->template->editable = $editable;
        $this->template->title = $title;
        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
        $this->template->organization = $organization;
    }

    protected function getEnrollmentType ($syllabus, $viewer)
    {
        $courseSection = null;
        $type = 'instructor';
        if ($sectionVersion = $syllabus->latestVersion->getCourseInfoSection())
        {
            $courseSection = $sectionVersion->resolveSection()->classDataCourseSection;
            if ($courseSection && $courseSection->enrollments->has($viewer->classDataUser))
            {
                $roles = $this->schema('Syllabus_AuthN_Role');
                $studentRole = $roles->findOne($roles->name->equals('Student'));
                if ($viewer->roles->has($studentRole))
                {
                    $type = 'student';
                }                    
            }
        }

        return [$type, $courseSection];
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
                // $newSyllabusVersion->absorbData($data['syllabus']);
                $newSyllabusVersion->title = $data['syllabus']['title'];
                $newSyllabusVersion->description = isset($data['syllabus']['description']) ? $data['syllabus']['description'] : '';
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
            	// $sortOrder = strlen($sortOrder) === 2 ? $sortOrder : '0'.$sortOrder;
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
        $temp = [];
        // save sort_order posted data
        if ($oldSyllabusVersion && $newSyllabusVersion)
        {
            foreach ($newSyllabusVersion->sectionVersions as $i => $sv)
            {     
                // $temp[] = 'attempt';
                $checkSv = $sv;
                if ($newSyllabusVersion->sectionVersions->getProperty($sv, 'inherited'))
                {
                    $checkSv = $sv->section->latestVersion;
                }
                if (isset($data['section']['properties']['sortOrder']) && isset($data['section']['properties']['sortOrder'][$checkSv->id]))
                {
                	$anyChange = true;
	            	$sortOrder = $data['section']['properties']['sortOrder'][$checkSv->id];
	            	// $sortOrder = strlen($sortOrder) === 2 ? $sortOrder : '0'.$sortOrder;
                    // $temp[$sv->id] = $sortOrder;
                    $newSyllabusVersion->sectionVersions->setProperty($sv, 'sort_order', $sortOrder);
                }
                $newSyllabusVersion->sectionVersions->save();
            }
            // echo "<pre>"; var_dump($temp); die;
            $newSyllabusVersion->save();
            
        }

        // update preview
        if ($anyChange)
        {
            $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
            $this->getScreenshotUrl($syllabus->id, $screenshotter, false);            
        }

        return [$anyChange, $newSyllabusVersion];
    }

    private function saveResourceToSyllabi ($resourceId, $syllabiIds)
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        $sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        $campusResources = $this->schema('Syllabus_Syllabus_CampusResource');
        $resources = $this->schema('Syllabus_Resources_Resources');
        $syllabiIds = array_reverse($syllabiIds);

        if ($campusResource = $campusResources->get($resourceId))
        {
            $numberUpdated = 0;
            foreach ($syllabiIds as $syllabusId)
            {
                if ($syllabus = $syllabi->get($syllabusId))
                {
                    $newResource = $this->schema('Syllabus_Resources_Resource')->createInstance();
                    $syllabusVersion = $syllabus->latestVersion;
                    $syllabusSectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
                    $resourcesSectionVersion = null;
                    $resourcesSection = null;
                    $realSection = null;
                    
                    foreach ($syllabusSectionVersions as $sv)
                    {
                        if (isset($sv->resources_id))
                        {
                            $resourcesSectionVersion = $sv;
                            $resourcesSection = $sv->section;
                            $resourcesSection->modifiedDate = new DateTime;
                            $realSection = $sv->resolveSection();
                            break;
                        }
                    }

                    if (!$resourcesSectionVersion)
                    {
                        $realSection = $resources->createInstance();
                        $resourcesSection = $sections->createInstance();
                        $resourcesSectionVersion = $sectionVersions->createInstance();
                        $realSection->save();
                        $resourcesSection->createdById = $viewer->id;
                        $resourcesSection->createdDate = new DateTime;
                        $resourcesSection->modifiedDate = new DateTime;
                        $resourcesSection->save();
                        $resourcesSectionVersion->title = 'Resources';
                        $resourcesSectionVersion->createdDate = new DateTime;
                        $resourcesSectionVersion->sectionId = $resourcesSection->id;
                        $resourcesSectionVersion->resources_id = $realSection->id;
                        $resourcesSectionVersion->save();
                        $syllabusVersion->sectionVersions->add($resourcesSectionVersion);
                        $sortOrder = @count($syllabusSectionVersions) + 1 ?? 1;
                        $syllabusVersion->sectionVersions->setProperty($resourcesSectionVersion, 'sort_order', $sortOrder);
                        $syllabusVersion->sectionVersions->setProperty($resourcesSectionVersion, 'is_anchored', true);
                        $syllabusVersion->save();
                        $syllabusVersion->sectionVersions->save();
                    }
                    $sortOrder = isset($realSection->resources) ? @count($realSection->resources)+1 : 1;
                    $resourceData = $campusResource->getData();
                    unset($resourceData['id']);
                    unset($resourceData['image']);
                    $newResource->absorbData($resourceData);
                    $newResource->campusResourcesId = $campusResource->id;
                    $newResource->resources_id = $realSection->id;
                    $newResource->sortOrder = $sortOrder;
                    $newResource->isCustom = false;
                    $newResource->save();

                    $syllabus->modifiedDate = new DateTime;
                    $syllabus->save();
                    $syllabusVersion->createdDate = new DateTime;
                    $syllabusVersion->save();

                    $numberUpdated++;
                }
            }

            $endOfMsg = $numberUpdated . (($numberUpdated > 1) ? ' syllabi.' : ' syllabus.');

            $results = [
                'message' => 'Success! You added the '.$campusResource->title.' resource to '.$endOfMsg,
                'status' => 'success',
                'data' => ''
            ];
        }
        else
        {
            $results = [
                'message' => 'An incorrect resource was attempted to be saved.',
                'status' => 'error',
                'data' => ''
            ];
        }

        return $results;
        // echo json_encode($results);
        // exit;  
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
                'syllabusVersion' => [
                    'id' => $syllabus->latestVersion->id
                ],
                'section' => [
                    'versionId' => 'new',
                    'realClass' => ['new' => 'Syllabus_Courses_Course'],
                    'extKey' => ['new' => 'course_id'],
                    'properties' => [
                        'sortOrder' => ['new' => '000'],
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

    private function hasSyllabusPermission ($syllabus, $user=null, $permission='view')
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
                    $organization = $this->schema('Syllabus_AcademicOrganizations_College')->get($id);
                    break;

                default:
                    break;
            }

            if (!$organization)
            {
                $this->accessDenied("Could not find any '{$type}' with id '{$id}'.");            
            }

            switch ($permission)
            {
                case 'delete':
                    $hasPermission = $organization->userHasRole($user, 'manager');
                    break;
                case 'edit':
                    $hasPermission = $organization->userHasRole($user, 'manager') || $organization->userHasRole($user, 'creator');
                    break;
                case 'view':
                case 'clone':
                default:
                    $hasPermission = $organization->userHasRole($user, 'member');
                    break;
            }
            
        }
        elseif ($permission !== 'view' && $syllabus->createdById !== $user->id)
        {
            $hasPermission = false;
        }
        elseif ($permission === 'view')
        {
            if ($syllabus->createdById !== $user->id)
            {
                if ($sectionVersion = $syllabus->latestVersion->getCourseInfoSection())
                {
                    $courseSection = $sectionVersion->resolveSection()->classDataCourseSection;
                    if ($courseSection && $courseSection->enrollments->has($user->classDataUser))
                    {
                        $roles = $this->schema('Syllabus_AuthN_Role');
                        $studentRole = $roles->findOne($roles->name->equals('Student'));
                        if ($user->roles->has($studentRole))
                        {
                            if ($syllabus->getShareLevel() === 'private')
                            {
                                $hasPermission = false;
                            }
                        }                    
                    }
                    else
                    {
                        $hasPermission = false;
                    }
                }
            }
        }
        if ($this->hasPermission('admin'))
        {
            $hasPermission = true;
        }

        return $hasPermission;        
    }

    public function getUpstreamSyllabi ($sectionVersion, $syllabus, $account)
    {
        $result = [];
        if ($sectionVersion)
        {
            $rs = pg_query("
            select distinct s.id from syllabus_syllabus_versions sv 
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
                $result[] = $row[0];
            }          
        }    

        return $result;    
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
                svsv.inherited = 'f' and svsv2.inherited = 'f'");

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
        $result = $this->getUpstreamSyllabi($sectionVersion, $syllabus, $account);

        return count($result) > 0;
    }

    public function hasDownstreamSection ($sectionVersion, $syllabus, $account)
    {
        $result = false;
        if ($sectionVersion)
        {
            if ($syllabus->templateAuthorizationId)
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
                    s.template_authorization_id = '{$syllabus->templateAuthorizationId}' and
                    s.template_authorization_id is not null and 
                    svsv2.inherited = 't'");
            }
            else
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
                    svsv2.inherited = 't'");                
            }


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

    public function iLearn ()
    {   
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id');
        $shareLevel = $syllabus->getShareLevel();

    }

    public function screenshot ()
    {
        $this->setScreenshotTemplate();

        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id');
        $syllabusVersion = $syllabusVersions->get($this->request->getQueryParameter('v')) ?? $syllabus->latestVersion;

        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);


        // $tokenHeader = $this->requireExists($this->request->getHeader('X-Custom-Header'));

        // $sid = $this->getRouteVariable('id');
        // $keyPrefix = sha1($sid) . '-';
        // $key = "{$keyPrefix}{$sid}";
        // $uid = Syllabus_Services_Screenshotter::CutUid($key);

        // if ($tokenHeader && $uid && ($tokenHeader == $uid))
        // {
        //     $this->setScreenshotTemplate();

        //     $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        //     $sections = $this->schema('Syllabus_Syllabus_Section');
            
        //     $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id');
        //     $syllabusVersion = $syllabusVersions->get($this->request->getQueryParameter('v')) ?? $syllabus->latestVersion;

        //     $this->template->sectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
        // }
        // else
        // {
        //     http_response_code (401);
        //     die;        
        // }
    }


    public function migrate ()
    {
        $viewer = $this->requireLogin();
        $newSyllabus = null;
        $roles = $this->schema('Syllabus_AuthN_Role');
        $studentRole = $roles->findOne($roles->name->equals('Student'));
        $isStudent = false;

        if ($viewer->roles->has($studentRole))
        {
            $this->response->redirect('syllabi');
        }
        if ($this->request->wasPostedByUser())
        {    
            $file = $this->schema('Syllabus_Files_File')->createInstance();
            $failure = $file->createFromRequest($this->request, 'file', false, 'application/octet-stream');
            
            if ($failure)
            {
                $this->flash('Wrong file type uploaded. Only Syllabus backup files (.bak) are allowed.', 'danger');
                $this->response->redirect('syllabus/migrate');
            }
            if ($file->isValid())
            {
                $file->uploadedBy = $this->getAccount();
                $file->moveToPermanentStorage();
                $file->save();

                // begin migration
                if ($json = file_get_contents($file->localFilename))
                {
                    list($errors, $newSyllabus) = $this->migrateData($json);
                    if (!$newSyllabus)
                    {
                        $this->flash('An error occurred during migration', 'danger');
                    }
                    else
                    {
                        $this->template->errors = $errors;
                        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
                        $newSyllabus = $syllabi->findOne($syllabi->createdById->equals($viewer->id));
                    }
                }
                $file->delete();
            }
            
            $this->template->errors = $file->getValidationMessages();
        }
        $this->template->newSyllabus = $newSyllabus;
    }

    private function migrateData ($json)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $viewer = $this->requireLogin();
        $now = new DateTime;

        $siteSettings = $this->getApplication()->siteSettings;
        $templateId = $siteSettings->getProperty('university-template-id');
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $startingTemplate = $this->requireExists($syllabi->get($templateId));
        $syllabus = $this->startWith($startingTemplate, true, true);
        $syllabusVersion = $syllabus->latestVersion;
        $syllabusVersion->createdDate = $now;
        $syllabusVersion->title = 'Migrated Syllabus on '. $syllabus->createdDate->format('F jS, Y - h:i a');
        $syllabusVersion->save();

        $errors = [];
        $success = false;
        $data = json_decode($json);
        $counter = 1;
        foreach ($data->modules as $type => $module)
        {   
            if (isset($module->items) && !empty($module->items))
            {
                try 
                {
                    $section = $this->schema('Syllabus_Syllabus_Section')->createInstance();
                    $sectionVersion = $this->schema('Syllabus_Syllabus_SectionVersion')->createInstance();
                    $section->createdDate = $now;
                    $section->modifiedDate = $now;
                    $section->createdById = $viewer->id;
                    $section->save();
                    $sectionVersion->createdDate = $now;
                    $sectionVersion->title = $module->module_custom_name ?? '';
                    $sectionVersion->sectionId = $section->id;
                    $realSectionKey = '';
                    switch ($type)
                    {
                        case 'assignments':
                            $realSectionKey = 'activities_id';
                            $sectionVersion->title = ($sectionVersion->title==='') ? 'Assignments' : $sectionVersion->title;
                            $realSection = $this->schema('Syllabus_Activities_Activities')->createInstance();
                            $realSection->save();
                            if (isset($module->items) && !empty($module->items))
                            {
                                foreach ($module->items as $i => $item)
                                {
                                    $realItem = $this->schema('Syllabus_Activities_Activity')->createInstance();
                                    $realItem->name = $item->assignment_title;
                                    $realItem->value = $item->assignment_value;
                                    $realItem->description = $item->assignment_desc;
                                    $realItem->sortOrder = $i + 1;
                                    $realItem->activities_id = $realSection->id;
                                    $realItem->save();
                                }                        
                            }

                            break;

                        case 'materials':
                            $realSectionKey = 'materials_id';
                            $sectionVersion->title = ($sectionVersion->title==='') ? 'Materials' : $sectionVersion->title;
                            $realSection = $this->schema('Syllabus_Materials_Materials')->createInstance();
                            $realSection->save();
                            $info = '';
                            if (isset($module->items) && !empty($module->items))
                            {
                                foreach ($module->items as $i => $item)
                                {
                                    $realItem = $this->schema('Syllabus_Materials_Material')->createInstance();
                                    $realItem->title = $item->material_title;
                                    $realItem->required = $item->material_required;
                                    $realItem->publishers = strip_tags(trim($item->material_info));
                                    $realItem->sortOrder = $i + 1;
                                    $realItem->materials_id = $realSection->id;
                                    $realItem->save();
                                    $info .= $item->material_info . '<br>';
                                }                        
                            }

                            $realSection->additionalInformation = $info;
                            $realSection->save();
                            break;

                        case 'methods':
                            $realSectionKey = 'objectives_id';
                            $sectionVersion->title = ($sectionVersion->title==='') ? 'Methods' : $sectionVersion->title;
                            $realSection = $this->schema('Syllabus_Objectives_Objectives')->createInstance();
                            $realSection->save();
                            if (isset($module->items) && !empty($module->items))
                            {
                                foreach ($module->items as $i => $item)
                                {
                                    $realItem = $this->schema('Syllabus_Objectives_Objective')->createInstance();
                                    $realItem->name = $item->method_title;
                                    $realItem->description = $item->method_text;
                                    $realItem->sortOrder = $i + 1;
                                    $realItem->objectives_id = $realSection->id;
                                    $realItem->save();
                                }                        
                            }

                            break;

                        case 'objectives':
                            $realSectionKey = 'objectives_id';
                            $sectionVersion->title = ($sectionVersion->title==='') ? 'Objectives' : $sectionVersion->title;
                            $realSection = $this->schema('Syllabus_Objectives_Objectives')->createInstance();
                            $realSection->save();
                            if (isset($module->items) && !empty($module->items))
                            {
                                foreach ($module->items as $i => $item)
                                {
                                    $realItem = $this->schema('Syllabus_Objectives_Objective')->createInstance();
                                    $realItem->name = $item->objective_title;
                                    $realItem->description = $item->objective_text;
                                    $realItem->sortOrder = $i + 1;
                                    $realItem->objectives_id = $realSection->id;
                                    $realItem->save();
                                }                        
                            }

                            break;

                        case 'policies':
                            $realSectionKey = 'policies_id';
                            $sectionVersion->title = ($sectionVersion->title==='') ? 'Policies' : $sectionVersion->title;
                            $realSection = $this->schema('Syllabus_Policies_Policies')->createInstance();
                            $realSection->save();
                            if (isset($module->items) && !empty($module->items))
                            {
                                foreach ($module->items as $i => $item)
                                {
                                    $realItem = $this->schema('Syllabus_Policies_Policy')->createInstance();
                                    $realItem->name = $item->policy_title;
                                    $realItem->description = $item->policy_text;
                                    $realItem->sortOrder = $i + 1;
                                    $realItem->policies_id = $realSection->id;
                                    $realItem->save();
                                }                        
                            }

                            break;

                        case 'schedules':
                            $realSectionKey = 'schedule_id';
                            $sectionVersion->title = ($sectionVersion->title==='') ? 'Schedule' : $sectionVersion->title;
                            $realSection = $this->schema('Syllabus_Schedules_Schedules')->createInstance();
                            $realSection->columns = 3;
                            $realSection->header1 = 'Date';
                            $realSection->header2 = 'Notes';
                            $realSection->header3 = 'Due';
                            $realSection->save();
                            if (isset($module->items) && !empty($module->items))
                            {
                                foreach ($module->items as $i => $item)
                                {
                                    $realItem = $this->schema('Syllabus_Schedules_Schedule')->createInstance();
                                    $date = $item->schedule_date;
                                    try 
                                    {
                                        $dateCol = $item->schedule_period === 'd' ? '<p>' : '<p>Week of ';
                                        $realItem->column1 = $dateCol.'<span data-timestamp="'.$date.'">'.$date.'</span></p>';
                                        $realItem->dateField = new DateTime($date);
                                    } 
                                    catch (exception $e) 
                                    {
                                        $errors[] = 'Invalid date for column 1, row '.($i+1).' with content: '.$item->schedule_date;
                                        $realItem->column1 = $item->schedule_date;
                                    }
                                    $realItem->column2 = $item->schedule_desc;
                                    $realItem->column3 = $item->schedule_due;
                                    $realItem->sortOrder = $i + 1;
                                    $realItem->schedules_id = $realSection->id;
                                    $realItem->save();
                                }                        
                            }

                            break;

                        case 'tas':
                            $realSectionKey = 'teaching_assistants_id';
                            $sectionVersion->title = ($sectionVersion->title==='') ? 
                                'Teaching Assistants' : $sectionVersion->title;
                            $realSection = $this->schema('Syllabus_TeachingAssistants_TeachingAssistants')->createInstance();
                            $realSection->save();
                            if (isset($module->items) && !empty($module->items))
                            {
                                foreach ($module->items as $i => $item)
                                {
                                    $realItem = $this->schema('Syllabus_TeachingAssistants_TeachingAssistant')->createInstance();
                                    $realItem->name = $item->ta_name;
                                    $realItem->email = $item->ta_email;
                                    $realItem->sortOrder = $i + 1;
                                    $realItem->teaching_assistants_id = $realSection->id;
                                    $realItem->save();
                                }                        
                            }

                            break;
                    }

                    $sectionVersion->$realSectionKey = $realSection->id;
                    $sectionVersion->save();
                    $syllabusVersion->sectionVersions->add($sectionVersion);
                    $syllabusVersion->sectionVersions->setProperty($sectionVersion, 'sort_order', $counter);
                    $syllabusVersion->sectionVersions->setProperty($sectionVersion, 'read_only', false);
                    $syllabusVersion->sectionVersions->setProperty($sectionVersion, 'inherited', false);
                    $syllabusVersion->sectionVersions->setProperty($sectionVersion, 'is_anchored', true);
                    $syllabusVersion->sectionVersions->setProperty(
                        $sectionVersion, 'log', 'Migrated from old Syllabus app.'
                    );
                    $syllabusVersion->sectionVersions->save();
                    $syllabusVersion->save();
                    $counter++;

                }
                catch (exception $e)
                {
                    $errors[] = 'Error adding section: ' . $type;
                }
            }          

            $success = true;
        }

        // move campus policies to bottom
        $total = count($syllabusVersion->sectionVersions);
        foreach ($syllabusVersion->sectionVersions as $i => $sv)
        {
            $sortOrder = ($i === 0) ? $total : $i;
            $syllabusVersion->sectionVersions->setProperty($sv, 'sort_order', $sortOrder);
        }
        $syllabusVersion->save();
        $syllabusVersion->sectionVersions->save();
        $syllabus->versions->add($syllabusVersion);
        $syllabus->versions->save();
        $syllabus->save();



        $this->getScreenshotUrl($syllabus->id);

        if (!$success)
        {
            $syllabusVersion->delete();
            $syllabus->delete();
            return null;
        }

        return [$errors, $syllabusVersion];
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