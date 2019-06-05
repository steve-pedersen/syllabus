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
            'syllabi'           => ['callback' => 'mySyllabi'],
            'syllabus/:id'      => ['callback' => 'edit',  ':id' => '[0-9]+|new'],
            'syllabus/courses'  => ['callback' => 'courseLookup'],
            'syllabus/start'    => ['callback' => 'start'],
            'syllabus/startwith/:id' => ['callback' => 'startWith', ':id' => '[0-9]+'],
        ];
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
                $this->sendError(400, 'Bad Request', 'ParameterRequired', "Could not find any '{$type}' with id '{$id}'.");            
            }

            $hasPermission = $organization->userHasRole($user, 'member');
        }
        elseif ($syllabus->createdById !== $user->id)
        {
            $hasPermission = false;
        }

        return $hasPermission;
    }

    public function startWith ()
    {
        $viewer = $this->requireLogin();
        $fromSyllabus = $this->requireExists($this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id'));
        
        if (!$this->hasCloningPermission($fromSyllabus, $viewer))
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

        $this->response->redirect(implode('/', $pathParts));
    }

    public function start ()
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $courseSections = $this->schema('Syllabus_ClassData_CourseSection');
        $departmentSchema = $this->schema('Syllabus_AcademicOrganizations_Department');
        $collegeSchema = $this->schema('Syllabus_AcademicOrganizations_College');

        // if based off of a course
        if ($courseSectionId = $this->request->getQueryParameter('course'))
        {
            $courseSection = $courseSections->get($courseSectionId);
            $this->template->courseSection = $courseSection;
            $this->template->pastCourseSyllabi = $courseSection->getRelevantPastCoursesWithSyllabi($viewer);
        }

        $orgs = [];
        $templates = [];
        foreach ($viewer->classDataUser->enrollments as $cs)
        {
            if (!isset($orgs[$cs->department->id]))
            {
                $templates[$cs->department->id] = $syllabi->find(
                    $syllabi->templateAuthorizationId->equals($cs->department->templateAuthorizationId),
                    ['orderBy' => '-createdDate', 'limit' => '4']
                );
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
                // TODO: update for 'clone' option
                $fromCourse = isset($courseSectionId) ? $courseSections->get($courseSectionId) : null;
                $templateAuthorizationId = isset($data['template']) ? $data['template'] : null;

                switch ($data['startingTemplate']) {

                    case 'university':
                        
                        if ($fromCourse)
                        {
                            list($success, $syllabusVersion) = $this->createCourseSyllabus('new', $fromCourse);
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
                            $pathParts[] = 'new';
                            $pathParts = array_filter($pathParts);
                            $this->response->redirect(implode('/', $pathParts));
                        }
                        else
                        {
                            $pathParts[] = 'new';
                            $pathParts = array_filter($pathParts);
                            // TODO: add language string check
                            $this->flash("
                                Your new syllabus draft includes all SF State requirements. You are able to choose which 
                                course it is for by first giving the syllabus a name and saving it, then adding a new 
                                'Course Information' section. If you want to use this syllabus draft as a starting point 
                                for any other courses, then visit your 'Courses' dashboard from the main menu.", 
                                'success'
                            );
                            $this->response->redirect(implode('/', $pathParts));
                        }
                        break;

                    case 'department':

                        break;

                    case 'clone':

                        break;

                    default:
                        $this->flash('An unknown error occurred', 'danger');
                        break;
                }
            }            
        }

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
     
        if ($this->request->wasPostedByUser())
        {      
            switch ($this->getPostCommand()) {

                case 'addsection':

                    if (isset($data['addSectionTop']) || $data['addSectionBot'])
                    {
                        $sectionClass = !empty($data['addSectionTop']) ? $data['addSectionTop'] : $data['addSectionBot'];
                    }

                    if (($sectionClass !== 'false') || ($sectionClass !== ''))
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
                    $genericSection->log = $syllabusVersion->sectionVersions->getProperty($sectionVersion, 'log');
                    $genericSection->readOnly = (bool)$syllabusVersion->sectionVersions->getProperty($sectionVersion, 'read_only');
                    $genericSection->isAnchored = (bool)$syllabusVersion->sectionVersions->getProperty($sectionVersion, 'is_anchored');
                    $genericSection->isAnchored = ($genericSection->isAnchored === null) ? true : $genericSection->isAnchored;
                    $genericSection->sortOrder = (isset($data['section']['properties']['sortOrder']) ?
                        $data['section']['properties']['sortOrder'][$sectionVersion->id] : 
                        $syllabusVersion->sectionVersions->getProperty($sectionVersion, 'sort_order')
                    );
                    $realSection = $sectionVersion->resolveSection();
                   
                    $this->template->realSection = $realSection;
                    $this->template->realSectionClass = get_class($realSection);
                    $this->template->sectionExtension = $sectionVersion->getExtensionByName(get_class($realSection));
                    $this->template->genericSection = $genericSection;
                    $this->template->currentSectionVersion = $sectionVersion;
                    $this->template->isUpstreamSection = $this->isUpstreamSection($sectionVersion, $syllabus, $viewer);
                    $this->template->hasDownstreamSection = $this->hasDownstreamSection($sectionVersion, $syllabus, $viewer);
                    break;

                case 'editsyllabus':
                    $this->template->editMetadata = true;
                    $this->template->syllabusVersion = $syllabusVersion;
                    break;

                case 'savesection':
                case 'savesyllabus':
                    $syllabus->templateAuthorizationId = $organization ? $organization->templateAuthorizationId : null;
                    list($updated, $syllabusVersion) = $this->saveSyllabus($syllabus);
                    if (!$updated)
                    {
                        $this->flash('No changes were made', 'warning');
                    }
                    $pathParts[] = $syllabusVersion->syllabus->id;
                    $pathParts = array_filter($pathParts);
                    $this->response->redirect(implode('/', $pathParts));
                    break;
            }
        }

        // determine which sections are editable
        $sectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
        foreach ($sectionVersions as $sv)
        {
            $sectionParentOrganization = null;
            if ($sv->uniqueSyllabiCount > 1)
            {
                $sectionParentOrganization = $sv->parentOrganization;
            }

            // section is read-only and belongs to this organization (not a parent one) 
            if (($sv->readOnly && $organization) || ($sv->readOnly && $organization && !$sectionParentOrganization))
            {
                $sv->canEditReadOnly = $organization->userHasRole($viewer, 'creator') || $organization->userHasRole($viewer, 'manager');
            }
            else
            {
                // if read only, then you can only edit it if it was created by the one viewing
                $sv->canEditReadOnly = !$sv->readOnly || ($sv->section->createdById === $viewer->id);
            }
        }

        $this->template->sidebarMinimized = true;
        $this->template->title = $title;
        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $sectionVersions;
        $this->template->sectionExtensions = $sectionExtensions;
        $this->template->userCourses = $currentCourses ?? $viewer->classDataUser->getCurrentEnrollments(); // TODO: Add only if needed
        $this->template->organization = $organization;
    }

    public function mySyllabi ()
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $courseSections = $this->schema('Syllabus_ClassData_CourseSection');

        switch ($mode = $this->request->getQueryParameter('mode', 'overview')) {
            case 'courses':
                
                $myCourses = $viewer->classDataUser->getCurrentEnrollments();
                $courses = [];
                foreach ($myCourses as $i => $courseSection)
                {
                    $i++;
                    $courseSyllabus = $syllabi->get($courseSection->syllabus_id);
                    $courseSection->courseSyllabus = $courseSyllabus;
                    $courseSection->createNew = $courseSyllabus ? false : true;
                    $courseSection->pastCourseSyllabi = $courseSection->getRelevantPastCoursesWithSyllabi($viewer);
                    $courseSection->image = "assets/images/testing0$i.jpg";
                    $courses[$courseSection->term][] = $courseSection;
                }

                $this->template->allCourses = $courses;
                break;
            
            case 'submissions':            
                
                break;

            case 'overview':
            default:
                $mySyllabi = $syllabi->find($syllabi->createdById->equals($viewer->id), ['orderBy' => '-createdDate', 'limit' => 20]);
                $this->template->syllabi = $mySyllabi;
                break;
        }

        if ($this->request->wasPostedByUser())
        {    
            $courseSection = $courseSections->get(key($this->getPostCommandData()));
            $data = $this->request->getPostParameters();

            switch ($this->getPostCommand()) {
                
                case 'courseNew':
                    list($success, $syllabusVersion) = $this->createCourseSyllabus('new', $courseSection);
                    if ($success)
                    {
                    	$this->flash('Your new course syllabus is ready for you to edit and add more sections.', 'success');
                    	$this->response->redirect('syllabus/' . $syllabusVersion->syllabus->id);
                    }
                    break;

                case 'courseClone':

                    if (isset($data['options']) && ($data['options'] === 'new'))
                    {
                        // TODO: NEED A $fromCourseSection and $toCourseSection
                        echo "<pre>"; var_dump('here cheese'); die;
                        list($success, $syllabusVersion) = $this->createCourseSyllabus('new', $courseSection);
                        $this->forward('syllabus/new?');
                    }

                    break;

                default:

            }
        }

        $this->template->mode = $mode;      
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
            $oldCount = isset($oldSyllabusVersion->sectionVersions) ? count($oldSyllabusVersion->sectionVersions) : 0;
            $newCount = count($newSyllabusVersion->sectionVersions);

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

            // TODO: decide if $data is safe to use
            // add new section version to this new syllabus version
            $defaultPosition = $newSyllabusVersion->sectionCount + 1;
            $newSyllabusVersion->sectionVersions->add($newSectionVersion);
            $newSyllabusVersion->sectionVersions->setProperty($newSectionVersion, 'sort_order', 
                isset($data['section']['properties']['sortOrder']) ? 
                    $data['section']['properties']['sortOrder'][$sectionVersionId] : 
                    $defaultPosition
            );
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
                    $newSyllabusVersion->sectionVersions->setProperty($sv, 'sort_order', 
                        $data['section']['properties']['sortOrder'][$sv->id]
                    );
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
        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');

        if ($versionId === 'new')
        {
            $syllabus = $syllabi->createInstance();

            $data = [
                'syllabus' => [
                    'title' => ('Syllabus for ' . $fromCourseSection->title),
                    'description' => ($fromCourseSection->getShortName() . ' course syllabus'),
                ],
                'section' => [
                    'versionId' => 'new',
                    'realClass' => ['new' => 'Syllabus_Courses_Course'],
                    'extKey' => ['new' => 'course_id'],
                    'properties' => [
                        'sortOrder' => ['new' => 1],
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