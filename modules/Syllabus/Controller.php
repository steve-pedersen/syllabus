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
            // '/'                         => ['callback' => 'mySyllabi'],
            'syllabi'                   => ['callback' => 'mySyllabi'],
            'syllabus/start'            => ['callback' => 'start'],
            'syllabus/:id/ajax'         => ['callback' => 'asyncSubmit', ':id' => '[0-9]+'],
            'syllabus/:id/view'         => ['callback' => 'view', ':id' => '[0-9]+'],
            'syllabus/:courseid/view'   => ['callback' => 'courseView'],
            'syllabus/:id/share'        => ['callback' => 'share', ':id' => '[0-9]+|courses'],
            'syllabus/:id/delete'       => ['callback' => 'delete', ':id' => '[0-9]+'],
            'syllabus/:id/print'        => ['callback' => 'print', ':id' => '[0-9]+'],
            'syllabus/:id/word'         => ['callback' => 'word', ':id' => '[0-9]+'],
            'syllabus/:id/export'       => ['callback' => 'export', ':id' => '[0-9]+'],
            'syllabus/:id/screenshot'   => ['callback' => 'screenshot', ':id' => '[0-9]+'],
            'syllabus/:id/ping'         => ['callback' => 'ping'],
            'syllabus/:id/thumbinfo'    => ['callback' => 'thumbInfo', ':id' => '[0-9]+'],
            'syllabus/submissions/file' => ['callback' => 'fileSubmission'],
            'syllabus/submissions'      => ['callback' => 'submissions'],
            'syllabus/submissions/:id'  => ['callback' => 'submissions', ':id' => '[0-9]+'],
            'syllabus/courses'          => ['callback' => 'courseLookup'],
            'syllabus/outcomes'         => ['callback' => 'outcomesLookup'],
            'syllabus/startwith/:id'    => ['callback' => 'startWith'],
            'syllabus/migrate'          => ['callback' => 'migrate'],
            'syllabus/autocomplete'     => ['callback' => 'autocompleteAccounts'],
            'syllabus/:courseid/logs'    => ['callback' => 'logs'],
            'syllabus/:courseid/ilearn' => ['callback' => 'fromIlearn'],
            'syllabus/:courseid/start'  => ['callback' => 'ilearnStart'],
            'syllabus/:courseid/upload' => ['callback' => 'uploadSyllabus'],
            'syllabus/:id/publish/:code'=> ['callback' => 'publishFromIlearn'],
            'syllabus/:courseid/publishreturn' => ['callback' => 'publishAndReturn'],
            'syllabus/:courseid/link/:code'=> ['callback' => 'getTemporaryLink'],
            'syllabus/notfound' => ['callback' => 'syllabusNotFound'],
            'syllabus/:id'              => ['callback' => 'edit'],
        ];
    }

    public function logs ()
    {
        $viewer = $this->requireLogin();
        $courseSection = $this->requireExists(
            $this->schema('Syllabus_ClassData_CourseSection')->get($this->getRouteVariable('courseid'))
        );
        $syllabus = $courseSection->syllabus;

        if (($syllabus->createdById !== $viewer->id) && !$this->hasSyllabusPermission($syllabus, $viewer, 'edit') && !$this->hasPermission('admin'))
        {
            $this->accessDenied('You are not an instructor of this course.');
        }

        $this->addBreadcrumb('syllabi?mode=courses', 'My Courses');
        $this->addBreadcrumb('syllabus/'.$courseSection->id.'/logs/', 'Logs');
        $logs = $this->schema('Syllabus_Syllabus_AccessLog');
        $courseLogs = $logs->find(
            $logs->courseSectionId->equals($courseSection->id), ['orderBy' => ['accountId', '+accessDate']]
        );

        $logs = [];
        $users = [];
        foreach ($courseLogs as $log)
        {
            $log->user->lastAccessDate = $log->accessDate;
            $users[$log->user->lastName . $log->user->username] = $log->user;
            $logs[$log->user->lastName . $log->user->username][] = $log;
        }
        ksort($logs);

        $nonViewUsers = [];
        foreach ($courseSection->enrollments as $user)
        {
            if ($courseSection->enrollments->getProperty($user, 'role') !== 'instructor')
            {
                if (!isset($users[$user->lastName . $user->id]))
                {
                    $nonViewUsers[$user->lastName . $user->id] = $user;
                }
            }
        }
        ksort($nonViewUsers);

        $this->template->courseSection = $courseSection;
        $this->template->logs = $logs;
        $this->template->users = $users;
        $this->template->nonViewUsers = $nonViewUsers;
    }

    public function fromIlearn ()
    {
        $this->requireLogin();
        $courseSection = $this->requireExists(
            $this->schema('Syllabus_ClassData_CourseSection')->get($this->getRouteVariable('courseid'))
        );

        $returnUrl = $this->request->getQueryParameter('returnUrl', '');

        if ($returnUrl === $this->baseUrl(''))
        {
            unset($_SESSION['ilearnReturnUrl']);
        }
        elseif ($returnUrl !== '')
        {
            $_SESSION['ilearnReturnUrl'] = $returnUrl;
        }
        
        $this->forward("syllabus/$courseSection->id/start", [
            'courseSection' => $courseSection,
            'fromIlearn' => true
        ]);
    }

    public function publishFromIlearn ()
    {
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id');
        $schema = $this->schema('Syllabus_Syllabus_PublishedSyllabus');
        $published = $schema->findOne($schema->syllabus_id->equals($syllabus->id));
        $published = $this->publishSyllabus($syllabus, 'all', $published);
        
        $code = $this->getRouteVariable('code');
        $app = $this->getApplication();
        $appKey = $app->getConfiguration()->getProperty('appKey', null);
        
        $returnArray = [];
        $returnArray['published'] = false;

        if ($code === $appKey)
        {
            if ($syllabus && $published) 
            {
                $returnArray['exists'] = true;
                $returnArray['url'] = $this->baseUrl('syllabus/' . $syllabus->id . '/view');
                $returnArray['edited'] = true;
                $returnArray['visible'] = true;
                $returnArray['published'] = true;
            } 
            else 
            {
                $returnArray['exists'] = false;
            }            
        }
        else
        {
            $this->notFound();
        }

        $return_json = json_encode($returnArray);
        echo($return_json);
        exit;      
    }

    public function ilearnStart ()
    {
        $viewer = $this->requireLogin();
        $courses = $this->schema('Syllabus_ClassData_CourseSection');
        $cid = $this->getRouteVariable('courseid');
        $courseSection = $this->getRouteVariable('courseSection', $courses->get($cid));
        $fromIlearn = $this->getRouteVariable('fromIlearn', false);
        $ilearnReturnUrl = isset($_SESSION['ilearnReturnUrl']) ? $_SESSION['ilearnReturnUrl'] : '';
        
        if (!$courseSection->isTaughtByUser($viewer) && !$this->hasPermission('admin'))
        {
            $this->accessDenied('You are not an instructor of this course.');
        }

        if ($courseSection && $courseSection->syllabus && $fromIlearn && $ilearnReturnUrl &&
            ($courseSection->syllabus->getShareLevel() === 'all')) 
        {
            $this->response->redirect($ilearnReturnUrl);
        }

        $isFileUnpublished = false;
        if ($courseSection && $courseSection->syllabus && $courseSection->syllabus->file && $fromIlearn) 
        {
            $isFileUnpublished = $courseSection->syllabus->getShareLevel() !== 'all' ? true : false;
        }

        if ($this->request->wasPostedByUser())
        {
            switch ($this->getPostCommand())
            {
                case 'existing':
                    $this->forward('syllabus/' . $courseSection->id . '/publishreturn');
                    break;

                case 'start':
                    // start from base template
                    $startingPoint = key($this->getPostCommandData());
                    if ($sid = $this->request->getPostParameter('existingFileSyllabus'))
                    {
                        $syllabus = $this->schema('Syllabus_Syllabus_Syllabus')->get($sid);
                        $syllabus->file->delete();
                        if ($syllabus->courseSection->syllabus_id == $syllabus->id)
                        {
                            $syllabus->courseSection->syllabus_id = null;
                            $syllabus->courseSection->save();
                        }
                        $syllabus->delete();
                    }

                    $this->forward('syllabus/start');
                    break;
            }
        }
        
        $this->template->userCameFromIlearn = $fromIlearn;
        $this->template->isFileUnpublished = $isFileUnpublished;
        $this->template->courseSection = $courseSection;
        $this->template->pastCourseSyllabi = $courseSection->getRelevantPastCoursesWithSyllabi($viewer, 3);
    }

    public function publishAndReturn ()
    {   
        if ($this->request->wasPostedByUser())
        {   
            $courseSection = $this->requireExists(
                $this->schema('Syllabus_ClassData_CourseSection')->get($this->getRouteVariable('courseid'))
            );
            $syllabus = $courseSection->syllabus;
            $schema = $this->schema('Syllabus_Syllabus_PublishedSyllabus');
            $published = $schema->findOne($schema->syllabusId->equals($syllabus->id));
            $published = $this->publishSyllabus($syllabus, 'all', $published);
            $returnTo = isset($_SESSION['ilearnReturnUrl']) ? $_SESSION['ilearnReturnUrl'] : '';

            $this->response->redirect($returnTo);   
        }
    }

    public function uploadSyllabus ()
    {
        $viewer = $this->requireLogin();

        if ($this->request->wasPostedByUser())
        {
            $courseSection = $this->requireExists(
                $this->schema('Syllabus_ClassData_CourseSection')->get($this->request->getQueryParameter('c'))
            );

            // upload syllabus file to be used for this course
            $results = [
                'message' => 'Server error when uploading.',
                'status' => 500,
                'success' => false
            ];

            $files = $this->schema('Syllabus_Files_File');
            if ($fid = $this->request->getPostParameter('uploadedFile'))
            {
                $oldFile = $files->get($fid);
                $oldFile->delete();
            }

            $file = $files->createInstance();
            $file->createFromRequest($this->request, 'file', false, Syllabus_Syllabus_Submission::$SyllabusFileTypes);
    
            if ($file->isValid())
            {
                $uploadedBy = (int)$this->request->getPostParameter('uploadedBy', $viewer->id);
                $file->uploaded_by_id = $uploadedBy;
                $file->moveToPermanentStorage();
                $file->save();
                
                $published = null;
                if ($courseSection->syllabus)
                {
                    $published = $courseSection->syllabus->getPublishedSyllabus($courseSection->syllabus);
                    $courseSection->syllabus->delete();
                }

                $syllabus = $this->schema('Syllabus_Syllabus_Syllabus')->createInstance();
                $syllabus->createdById = $uploadedBy;
                $syllabus->createdDate = $syllabus->modifiedDate =  new DateTime;
                $syllabus->file_id = $file->id;
                $syllabus->course_section_id = $courseSection->id;
                $syllabus->save();

                $courseSection->syllabus_id = $syllabus->id;
                $courseSection->save();

                if ($published)
                {
                    $published->syllabusId = $syllabus->id;
                    $published->save();
                }

                // TODO: update based on iLearn API
                $ilearnReturnUrl = isset($_SESSION['ilearnReturnUrl']) ? $_SESSION['ilearnReturnUrl'] : '';

                $results = [
                    'message' => 'Your syllabus has been uploaded.',
                    'status' => 200,
                    'success' => true,
                    'fileSrc' => 'files/' . $file->id . '/download',
                    'fileName' => $file->remoteName,
                    'ilearnReturnUrl' => $ilearnReturnUrl,
                    'sid' => $syllabus->id,
                    'cid' => $courseSection->id,
                    'fid' => $file->id,
                ];

                if ($ilearnReturnUrl)
                {
                    $this->flash(
                        'Your syllabus file has been uploaded for this course section. ' .
                        "<a href='$ilearnReturnUrl'>Return to iLearn</a>."
                    );
                }
            }
            else
            {
                $messages = 'Incorrect file type or file too large.';
                $results['status'] = $messages !== '' ? 400 : 422;
                $results['message'] = $messages;
            }

            echo json_encode($results);
            exit;  
        }    

        $sections = $this->schema('Syllabus_ClassData_CourseSection');
        $this->template->courseSection = $sections->get($this->getRouteVariable('courseid'));
        $this->template->viewer = $viewer;
    }

    public function submissions ()
    {
        $viewer = $this->requireLogin();
        $submissionId = $this->getRouteVariable('id');

        if (!$this->request->wasPostedByUser())
        {
            $this->addBreadcrumb('syllabi', 'Home');
            $this->addBreadcrumb('syllabi?mode=submissions', 'Submissions');

            $submission = $this->requireExists(
                $this->schema('Syllabus_Syllabus_Submission')->get($submissionId)
            );
            $this->addBreadcrumb('syllabus/submissions/' . $submission->id, 'Review Submission');

            $this->template->courseSection = $submission->courseSection;
            $this->template->submission = $submission;
            $this->template->account = $viewer;
        }

        if ($this->request->wasPostedByUser())
        {
            $data = $this->request->getPostParameters();
            $syllabusId = key($this->getPostCommandData());
            $syllabus = $this->schema('Syllabus_Syllabus_Syllabus')->get($syllabusId);
            $submissions = $this->schema('Syllabus_Syllabus_Submission');
            $submission = $submissions->findOne(
                $submissions->course_section_id->equals($syllabus->courseSection->id)
            );

            $submission->syllabus_id = $syllabus->id;
            // $submission->file_id = null;
            $submission->submitted_by_id = $viewer->id;
            $submission->modifiedDate = new DateTime;
            $submission->submittedDate = new DateTime;
            $submission->status = $submission->campaign->required ? 'pending' : 'approved';
            $submission->log .= "<li>
                Submitted syllabus #{$syllabus->id} on {$submission->modifiedDate->format('F jS, Y - h:i a')}.
            </li>";
            $submission->campaign->log .= "<li>
                Submission #{$submission->id} set to '{$submission->status}' on {$submission->modifiedDate->format('F jS, Y - h:i a')}.
            </li>";
            $submission->save();
            $submission->campaign->save();

            $this->flash('Syllabus submitted!');
            $this->response->redirect('syllabi?mode=submissions&c=' . $submission->course_section_id);
        }
    }

    public function fileSubmission ()
    {
        $viewer = $this->requireLogin();
        $submissions = $this->schema('Syllabus_Syllabus_Submission');

        if (!$this->request->wasPostedByUser() && $this->request->getQueryParameter('upload'))
        {
            $this->addBreadcrumb('syllabi', 'Home');
            $this->addBreadcrumb('syllabi?mode=submissions', 'Submissions');

            // syllabus file upload
            $courseSection = $this->requireExists(
                $this->schema('Syllabus_ClassData_CourseSection')->get($this->request->getQueryParameter('c'))
            );
            $this->addBreadcrumb('syllabus/submissions?upload=true&c=' . $courseSection->id, 'Submit File');

            $condition = $submissions->allTrue(
                $submissions->course_section_id->equals($courseSection->id),
                $submissions->deleted->isNull()->orIf($submissions->deleted->isFalse())
            );
            $submission = $submissions->findOne($condition, ['orderBy' => 'modifiedDate']);

            $this->template->fileUpload = true;
            $this->template->courseSection = $courseSection;
            $this->template->submission = $submission;
            $this->template->account = $viewer;
        }
        elseif (!$this->request->wasPostedByUser())
        {
            $this->response->redirect('syllabi?mode=submissions');
        }

        $results = [
            'message' => 'Server error when uploading.',
            'status' => 500,
            'success' => false
        ];


        if ($this->request->wasPostedByUser())
        {
            $data = $this->request->getPostParameters();

            $courseSection = $this->requireExists(
                $this->schema('Syllabus_ClassData_CourseSection')->get($this->request->getQueryParameter('c'))
            );

            $files = $this->schema('Syllabus_Files_File');
            $file = $files->createInstance();
            $file->createFromRequest($this->request, 'file', true, Syllabus_Syllabus_Submission::$SyllabusFileTypes);

            if ($file->isValid())
            {
                $uploadedBy = (int)$this->request->getPostParameter('uploadedBy');
                $file->uploaded_by_id = $uploadedBy;
                $file->moveToPermanentStorage();
                $file->save();

                $condition = $submissions->allTrue(
                    $submissions->course_section_id->equals($courseSection->id),
                    $submissions->deleted->isNull()->orIf($submissions->deleted->isFalse())
                );
                $submission = $submissions->findOne($condition, ['orderBy' => 'modifiedDate']);
                $submission->submitted_by_id = $uploadedBy;
                $submission->file_id = $file->id;
                $submission->status = 'pending';
                $submission->modifiedDate = new DateTime;
                $submission->submittedDate = new DateTime;
                $submission->log .= "<li>
                    Submitted as file #{$file->id} on {$submission->modifiedDate->format('F jS, Y - h:i a')}.
                </li>";
                $submission->campaign->log .= "<li>
                    Submission #{$submission->id} set to 'pending' on {$submission->modifiedDate->format('F jS, Y - h:i a')}.
                </li>";
                $submission->save();
                $submission->campaign->save();


                $results = [
                    'message' => 'Your syllabus has been uploaded.',
                    'status' => 200,
                    'success' => true,
                    'fileSrc' => 'files/' . $file->id . '/download',
                    'fileName' => $file->remoteName
                ];

                echo json_encode($results);
                exit;  
            }
            else
            {
                $messages = 'Incorrect file type or file too large.';
                $results['status'] = $messages !== '' ? 400 : 422;
                $results['message'] = $messages;
            }
        }
    }

    public function mySyllabi ()
    {
        $viewer = $this->requireLogin();
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $courseSections = $this->schema('Syllabus_ClassData_CourseSection');
        $offset = 0;
        $limit = 11;

        $siteSettings = $this->getApplication()->siteSettings;
        $userId = $siteSettings->getProperty('university-template-user-id');
        $templateId = $siteSettings->getProperty('university-template-id');
        if (($viewer->id != $userId) && !$this->hasPermission('admin'))
        {
            $this->requireExists($templateId);
        }
        // $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
        
        $roles = $this->schema('Syllabus_AuthN_Role');
        $studentRole = $roles->findOne($roles->name->equals('Student'));
        $facultyRole = $roles->findOne($roles->name->equals('Faculty'));
        $isStudent = false;
        if (!$viewer->roles->has($facultyRole) && $viewer->roles->has($studentRole))
        {
            $isStudent = true;
        }

        switch ($mode = $this->request->getQueryParameter('mode', 'overview')) {

            case 'courses':
                if ($isStudent)
                {
                    $this->response->redirect('syllabi');
                }
                $logs = $this->schema('Syllabus_Syllabus_AccessLog');
                $myCourses = $viewer->classDataUser->getRecentAndCurrentEnrollments();
                $courses = [];
                foreach ($myCourses as $i => $courseSection)
                {
                    $index = $i % 5;
                    if ($courseSyllabus = $syllabi->get($courseSection->syllabus_id))
                    {
                        $courseSyllabus->viewUrl = $this->baseUrl("syllabus/$courseSection->id/view");
                        if (!$courseSyllabus->file)
                        {
                            $courseSyllabus->hasCourseSection = false;
                            foreach ($courseSyllabus->latestVersion->getSectionVersionsWithExt(true) as $sv)
                            {
                                if (isset($sv->extension) && $sv->extension->getExtensionKey() === 'course_id' && isset($sv->resolveSection()->externalKey))
                                {
                                    $courseSyllabus->hasCourseSection = true;
                                    break;
                                }                        
                            }                              
                        }
                    }
                    $courseSection->logs = $logs->find(
                        $logs->courseSectionId->equals($courseSection->id), ['orderBy' => ['accountId', '+accessDate']]
                    );
                    $courseSection->courseSyllabus = $courseSyllabus;
                    $courseSection->createNew = $courseSyllabus ? false : true;
                    $courseSection->pastCourseSyllabi = $courseSection->getRelevantPastCoursesWithSyllabi($viewer);
                    $courses[$courseSection->term][] = $courseSection;
                }

                $focus = $this->request->getQueryParameter('f');
                $this->template->focus = $focus;
                $this->template->returnTo = 'syllabi?mode=courses';
                $this->template->coursesView = true;
                $this->template->allCourses = $courses;
                break;
            
            case 'submissions':            

                $myCourses = $viewer->classDataUser->getRecentAndCurrentEnrollments();
                $courses = [];
                foreach ($myCourses as $i => $courseSection)
                {
                    $index = $i % 5;
                    if ($courseSyllabus = $syllabi->get($courseSection->syllabus_id))
                    {
                        $courseSyllabus->viewUrl = $this->baseUrl("syllabus/$courseSyllabus->id/view");
                    }
                    $courseSection->courseSyllabus = $courseSyllabus;
                    $courseSection->createNew = $courseSyllabus ? false : true;
                    $courseSection->pastCourseSyllabi = $courseSection->getRelevantPastCoursesWithSyllabi($viewer);
                    $courses[$courseSection->term][] = $courseSection;
                }

                $this->template->submittedCourseId = $this->request->getQueryParameter('c');
                $this->template->returnTo = 'syllabi?mode=submissions';
                $this->template->coursesView = false;
                $this->template->allCourses = $courses;               
                break;

            case 'overview':
            default:

                if ($isStudent)
                {
                    $myCourses = $viewer->classDataUser->getRecentAndCurrentEnrollments();
                    $courses = [];
                    foreach ($myCourses as $i => $courseSection)
                    {
                        $index = $i % 5;
                        $courseSyllabus = $syllabi->get($courseSection->syllabus_id);
                        $courseSection->courseSyllabus = ($courseSyllabus && $courseSyllabus->shareLevel === 'all') ? 
                            $courseSyllabus : null;
                        $courseSection->createNew = false;
                        $courses[$courseSection->term][] = $courseSection;
                    }
                    $this->template->allCourses = $courses;
                }
                elseif ($this->hasPermission('syllabus list'))
                {
                    $campusResources = $this->schema('Syllabus_Syllabus_CampusResource');
                    $guideDocs = $this->schema('Syllabus_Syllabus_SharedResource');
                    $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
                    $searchQuery = $this->request->getQueryParameter('search');
                    $options = ['orderBy' => ['-modifiedDate', '-createdDate'], 'limit' => $limit, 'offset' => $offset];
                    $this->template->searchQuery = $searchQuery;

                    if (!empty($searchQuery))
                    {
                        $userSyllabi = $this->searchSyllabi($searchQuery, $viewer, $options);
                    }
                    elseif (!$this->hasPermission('admin'))
                    {
                        $userSyllabi = $syllabi->find(
                            $syllabi->createdById->equals($viewer->id)->andIf(
                                $syllabi->templateAuthorizationId->isNull()
                            ), 
                            $options
                        );                    
                    }
                    else
                    {
                        $userSyllabi = $syllabi->find(
                            $syllabi->createdById->equals($viewer->id), 
                            $options
                        );
                    }
                  
                    foreach ($userSyllabi as $userSyllabus)
                    {
                        if ($cs = $userSyllabus->getCourseSection())
                        {
                            $userSyllabus->viewUrl = $this->baseUrl("syllabus/$cs->id/view");
                            $userSyllabus->hasCourseSection = true;
                        }
                        else
                        {
                            $userSyllabus->viewUrl = $this->baseUrl("syllabus/$userSyllabus->id/view");
                            $userSyllabus->hasCourseSection = false;
                        }
                        // if (!$userSyllabus->file)
                        // {
                        //     $userSyllabus->hasCourseSection = false;
                        //     foreach ($userSyllabus->latestVersion->getSectionVersionsWithExt(true) as $sv)
                        //     {
                        //         if (isset($sv->extension) && $sv->extension->getExtensionKey() === 'course_id' && isset($sv->resolveSection()->externalKey))
                        //         {
                        //             $userSyllabus->hasCourseSection = true;
                        //             break;
                        //         }                        
                        //     }
                        // }
                        // $userSyllabus->viewUrl = $this->baseUrl("syllabus/$userSyllabus->id/view");
                    }
                    $this->template->campusResources = $campusResources->find(
                        $campusResources->deleted->isFalse()->orIf($campusResources->deleted->isNull()),
                        ['orderBy' => ['title']]
                    );   
                    $this->template->guideDocs = $guideDocs->find(
                        $guideDocs->active->isTrue()->andIf($guideDocs->active->isNotNull()),
                        ['orderBy' => ['sortOrder', 'title']]
                    );
                    $this->template->syllabi = $userSyllabi;        
                }

                $authZ = $this->getAuthorizationManager();
                $syllabusRoles = $this->schema('Syllabus_Syllabus_Role');
                $syllabusAzids = $authZ->getObjectsForWhich($viewer, 'syllabus edit');
                $this->template->syllabusRoles = $syllabusRoles->getByAzids($syllabusAzids);
                $this->template->isStudent = $isStudent;   
                $this->template->ctrl = $this;        
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
                    $sid = $data['courseSyllabus'];
                    $pastSyllabus = isset($sid) ? $syllabi->get($sid) : null;
  
                    if ($pastSyllabus && $this->hasSyllabusPermission($pastSyllabus, $viewer, 'clone'))
                    {
                        $newSyllabus = $this->startWith($pastSyllabus, true);
                        $newSyllabusVersion = $this->updateCourseSyllabus($pastSyllabus, $newSyllabus, $courseSection);
                        
                        $this->flash('Your new course syllabus is ready for you to edit.', 'success');
                        $this->response->redirect('syllabus/' . $newSyllabusVersion->syllabus->id);
                    }
                    break;
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
        
        // $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
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
        $facultyRole = $roles->findOne($roles->name->equals('Faculty'));
        if (!$viewer->roles->has($facultyRole) && $viewer->roles->has($studentRole) || 
            !$this->hasPermission('syllabus edit'))
        {
            $this->accessDenied('nope');
        }

        $templatesAvailable = false;
        $pastCourseSyllabi = null;
        $courseSection = null;

        // If starting for a particular course, get relevant past syllabi (e.g. same course diff sections)
        $courseSectionId = $this->request->getQueryParameter('course') ?? $this->request->getPostParameter('course');
        if ($courseSectionId)
        {
            $courseSection = $courseSections->get($courseSectionId);
            $pastCourseSyllabi = $courseSection->getRelevantPastCoursesWithSyllabi($viewer);

            $this->template->courseSection = $courseSection;
            $this->template->pastCourseSyllabi = $pastCourseSyllabi;
        }
        else
        {
            // Fetch 4 most recent syllabi
            $userSyllabi = $syllabi->find(
                $syllabi->createdById->equals($viewer->id), 
                ['orderBy' => ['-modifiedDate', '-createdDate'], 'limit' => 4]
            );
            $templatesAvailable = $templatesAvailable || !empty($userSyllabi);

            $this->template->syllabi = $userSyllabi;
        }

        // Fetch Departments and Department Templates
        $orgs = [];
        $templates = [];
        list($orgs, $templates) = $viewer->classDataUser->getDepartmentsAndTemplates($this);
        $temp = $templates;
        $templatesAvailable = $templatesAvailable || !empty(array_shift($temp));
        $this->template->organizations = $orgs;
        $this->template->templates = $templates;

        // Creating an org template
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
        else
        {
            $this->template->instructorView = true;
        }
        $pathParts = [];
        $pathParts[] = $this->getRouteVariable('routeBase');
        $pathParts[] = 'syllabus';

        // If the only option is to start from Base, then auto choose it and begin
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

                // Build for course
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
                // Build from Dept. Template
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
                // Build from other syllabus
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
            $id = $this->getRouteVariable('id');
            if (!($fromSyllabus = $this->schema('Syllabus_Syllabus_Syllabus')->get($id)))
            {
                $course = $this->schema('Syllabus_ClassData_CourseSection')->get($id);
                $fromSyllabus = $course ? $course->syllabus : null;
            }
            $this->requireExists($fromSyllabus);
        }
        
        if ($toCourse = $this->request->getQueryParameter('to'))
        {
            $toCourse = $this->schema('Syllabus_ClassData_CourseSection')->get($toCourse);
        }

        if (!$baseTemplate && !$this->hasSyllabusPermission($fromSyllabus, $viewer, 'clone'))
        {
            $this->sendError(403, 'Forbidden', 'Non-Member', 
                'You must be a member of this organization in order to use this template.');
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

        if ($toCourse)
        {   
            $toSyllabus->save();
            $toSyllabusVersion = $this->updateCourseSyllabus($fromSyllabus, $toSyllabus, $toCourse, $toSyllabusVersion);
        }

        // $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
        // $this->getScreenshotUrl($toSyllabus->id, $screenshotter, false);     

        $this->flash(
            'Your Syllabus has been cloned. The new clone has "(Copy)" appended to it\'s title metadata.', 
            'success'
        );

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

        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');

        $syllabus = @$syllabi->get($this->getRouteVariable('id'));
        if (!$syllabus)
        {
            $courseSection = $this->schema('Syllabus_ClassData_CourseSection')->get($this->getRouteVariable('id'));
            $syllabus = @$courseSection->syllabus ?? $syllabi->createInstance();
        }
        // $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id', 
        //     ['allowNew' => $this->hasPermission('admin')]
        // );
        if (!$this->hasPermission('admin') && !$this->hasSyllabusPermission($syllabus, $viewer, 'edit'))
        {
            $this->accessDenied("You do not have edit access for this syllabus.");
        }

        if ($syllabus->file)
        {
            list($type, $courseSection) = $this->getEnrollmentType($syllabus, $viewer);
            // if teacher, send to upload. if student, send to download
            switch ($type)
            {
                case 'student':
                    $this->response->redirect("files/$syllabus->file_id/download/syllabus");
                case 'instructor':
                    $this->forward("syllabus/$courseSection->id/start", ['courseSection' => $courseSection]);
                default:
                    $this->accessDenied('You do not have permission to download this syllabus.');
                    break;
            }
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

                case 'importsection':

                    $importables = $this->schema('Syllabus_Syllabus_ImportableSection');
                    $realSectionClass = key($this->getPostCommandData());
                    $realSection = $this->schema($realSectionClass)->createInstance();
                    $realSectionExtension = $sectionVersions->createInstance()->getExtensionByRecord($realSectionClass);
                    $extKey = $realSectionExtension->getExtensionKey();

                    if (!isset($data['section']['versionId']) || $data['section']['versionId'] === 'new')
                    {
                        list($syllabusVersion, $newSectionVersionId) = $this->saveSection($syllabus, $syllabusVersion, $extKey, $organization);
                        $sectionVersion = $sectionVersions->get($newSectionVersionId);
                    }
                    else
                    {
                        $sectionVersion = $sectionVersions->get($data['section']['versionId']);
                    }

                    if (isset($data['section']['real']['importable']))
                    {
                        foreach ($data['section']['real']['importable'] as $importableId)
                        {
                            $importable = $importables->get($importableId);
                            $copied = $sectionVersion->resolveSection()->copyImportables(
                                $importable->section->latestVersion->resolveSection()
                            );
                            $sectionVersion->resolveSection()->save();
                        }
                    }

                    $pathParts[] = $syllabusVersion->syllabus->id . '?edit=' . $sectionVersion->id;
                    $pathParts = array_filter($pathParts);
                    
                    $this->flash($realSectionExtension->getDisplayName() . ' added.');
                    $this->response->redirect(implode('/', $pathParts) . '&i=true');
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

                    $returnToEditingSection = false;
                    $newSection = false;
                    $now = new DateTime;
                    if ($this->request->getPostParameter('sortOrderUpdate'))
                    {
                        if (($sectionVersionId = $this->request->getQueryParameter('edit')) && 
                            ($this->request->getQueryParameter('edit') !== 'metadata'))
                        {
                            $returnToEditingSection = true;
                        }
                        elseif (($realSectionName = $this->request->getQueryParameter('add')))
                        {
                            $returnToEditingSection = true;
                            $newSection = true;
                        }
                    }

                    if (!$syllabus->templateAuthorizationId)
                    {
                        $syllabus->templateAuthorizationId = $organization ? $organization->templateAuthorizationId : null;
                    }
                    
                    list($updated, $syllabusVersion) = $this->saveSyllabus($syllabus);
                    if ($updated)
                    {
                        if (isset($_SESSION['ilearnReturnUrl']))
                        {
                            $url = $_SESSION['ilearnReturnUrl'];
                            $this->flash("Syllabus saved. <a href='$url'>Return to iLearn.</a>");
                        } 
                        else
                        {
                            $this->flash('Syllabus saved.', 'success');
                        }
                    }
                    $pathParts[] = $syllabusVersion->syllabus->id;
                    $pathParts = array_filter($pathParts);
                    if ($returnToEditingSection)
                    {
                        $newSectionVersionId = $sectionVersionId;
                        foreach ($syllabusVersion->sectionVersions as $sv)
                        {
                            if ($newSection && $sv->getThisExtension()->getExtensionName() === $realSectionName && $sv->createdDate >= $now)
                            {
                                $newSectionVersionId = $sv->id;
                                break;                             
                            }
                            elseif ($sv->createdDate >= $now)
                            {
                                $newSectionVersionId = $sv->id;
                                break;
                            }
                        }
                        $this->flash('Syllabus section order has been updated.', 'success');
                        $this->response->redirect(implode('/', $pathParts) . '?edit=' . $newSectionVersionId);
                    }
                    else
                    {
                        $this->response->redirect(implode('/', $pathParts));    
                    }
                    
                    break;

                case 'share':
                case 'unshare':
                    $this->forward("syllabus/$syllabus->id/share", ['returnTo' => "syllabus/$syllabus->id"]);
                    break;
            }
        }

        // EDIT METADATA
        if (!$this->request->wasPostedByUser() && ('metadata' === $this->request->getQueryParameter('edit')))
        {
            $this->template->editMetadata = true;
            $this->template->syllabusVersion = $syllabusVersion;
        }

        // ADD SECTION
        if (!$this->request->wasPostedByUser() && ($realSectionName = $this->request->getQueryParameter('add')))
        {
            // if ($realSectionName === 'learning_outcomes')
            // {
            //     $this->flash('The Student Learning Outcomes section type is unavailable at this time.', 'danger');
            //     $this->response->redirect('syllabus/' . $syllabus->id);
            // }
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
                if ($realSectionExtension::getExtensionName() === 'course' || $realSectionExtension::getExtensionName() === 'learning_outcomes')
                {
                    $currentCourses = $viewer->classDataUser->getCurrentEnrollments();
                    if ($syllabusVersion->getCourseInfoSection() && $syllabusVersion->getCourseInfoSection()->resolveSection())
                    {
                        $this->template->courseInfoSelected = $syllabusVersion->getCourseInfoSection()->resolveSection();
                    }
                }

                if ($realSectionClass === 'Syllabus_Instructors_Instructors')
                {
                    $profiles = $this->schema('Syllabus_Instructors_Profile');
                    $profileData = null;
                    if ($data = $profiles->createInstance()->findProfileData($viewer))
                    {
                        if (!empty($data) && isset($data['instructor']) && (!isset($data['syllabus']) || 
                            isset($data['syllabus']) && $data['syllabus']->id !== $syllabus->id))
                        {
                            $profileData = $data['instructor'];
                        }
                    }
                    $this->template->profileData = $profileData;
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
                        ['orderBy' => ['title']]
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
                $this->template->importableSections = $realSectionExtension->getImportableSections();
            }
            else
            {
                $this->flash('Invalid section type.', 'danger');
                $this->response->redirect('syllabus/' . $syllabus->id);
            }
        }

        // EDIT SECTION
        if (!$this->request->wasPostedByUser() && ($sectionVersionId = $this->request->getQueryParameter('edit')) && 
            $this->request->getQueryParameter('edit') !== 'metadata')
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
            if ($realSectionExtension->getExtensionName() === 'course' || $realSectionExtension::getExtensionName() === 'learning_outcomes')
            {
                $currentCourses = $viewer->classDataUser->getCurrentEnrollments();
                if ($syllabusVersion->getCourseInfoSection() && $syllabusVersion->getCourseInfoSection()->resolveSection())
                {
                    $this->template->courseInfoSelected = $syllabusVersion->getCourseInfoSection()->resolveSection();
                }
            }
            elseif (get_class($realSection) === 'Syllabus_Resources_Resources')
            {
                $campusResources = $this->schema('Syllabus_Syllabus_CampusResource');
                $this->template->campusResources = $campusResources->find(
                    $campusResources->deleted->isFalse()->orIf($campusResources->deleted->isNull()),
                    ['orderBy' => ['title']]
                );
            }
            elseif ($realSectionExtension->getExtensionName() === 'instructors')
            {
                $profiles = $this->schema('Syllabus_Instructors_Profile');
                $profileData = null;
                if ($profile = $profiles->findOne($profiles->account_id->equals($viewer->id)))
                {
                    $profileData = $profile;
                }
                elseif ($data = $profiles->createInstance()->findProfileData($viewer))
                {
                    if ((!empty($data) && isset($data['instructor']) && isset($data['syllabus']) && 
                        $data['syllabus']->id !== $syllabus->id))
                    {
                        $profileData = $data['instructor'];
                    }
                }
                $this->template->profileData = $profileData;
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
            
            // NOTE: Removed for now
            // $this->template->hasDownstreamSection = $this->hasDownstreamSection($sectionVersion, $syllabus, $viewer) &&
            //     !$this->isInheritedSection($sectionVersion, $syllabus->templateAuthorizationId);
            // NOTE: Replaced with this:
            $this->template->hasDownstreamSection = false;
            
            $this->template->editUri = '#section' . $realSectionExtension->getExtensionName() . 'Edit';
            $this->template->importableSections = $realSectionExtension->getImportableSections();
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

        $syllabus->viewUrl = $this->baseUrl("syllabus/$syllabus->id/view");
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
        $this->template->returnTo = "syllabus/$syllabus->id";
        $this->template->activeStudents = $syllabusVersion->getActiveStudentsEstimation($this) ?? 0;
        $this->template->viewer = $viewer;
        $this->template->justImported = $this->request->getQueryParameter('i', false);
    }

    public function getTemporaryLink ()
    {
        $courseSection = $this->requireExists(
            $this->schema('Syllabus_ClassData_CourseSection')->get($this->getRouteVariable('courseid'))
        );

        $code = $this->getRouteVariable('code');
        $appKey = $this->getApplication()->getConfiguration()->getProperty('appKey', null);
        
        $response = [];

        if ($code === $appKey)
        {
            $tempLink = $this->schema('Syllabus_Syllabus_TemporaryLink')->createInstance()->generate($courseSection);
            $response['url'] = $tempLink->getUrl();
            $response['status'] = 200;
            $response['message'] = 'One-time view/download link created.';
        }
        else
        {
            $response['status'] = 403;
            $response['message'] = 'Invalid API Key';
        }

        $return_json = json_encode($response);
        echo($return_json);
        exit;      
    }

    public function share ()
    {
        $viewer = $this->requireLogin();
        $syllabus = $this->requireExists($this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id'));
        $syllabusVersion = $syllabus->latestVersion;
        $publishSchema = $this->schema('Syllabus_Syllabus_PublishedSyllabus');
        $published = $this->getPublishedSyllabus($syllabus);

        if (!$this->hasPermission('admin') && !$this->hasSyllabusPermission($syllabus, $viewer, 'share'))
        {
            $this->accessDenied("You do not have share access for this syllabus.");
        }

        $this->setPageTitle('Share Syllabus');
        $this->addBreadcrumb('syllabus/'.$syllabus->id, 'Edit Syllabus');
        $this->addBreadcrumb('syllabus/'.$syllabus->id.'/share', 'Share');

        if ($this->request->wasPostedByUser())
        {   
            switch ($this->getPostCommand()) {
                case 'share':
                    $returnTo = $this->request->getPostParameter('returnTo') ?? $this->getRouteVariable('returnTo');
                    $published = $this->publishSyllabus($syllabus, 'all', $published);

                    $this->flash(
                        'Syllabus has been set to shared. All enrolled students are now able to access it.', 
                        'success'
                    );
                    break;

                case 'unshare':
                    $returnTo = $this->request->getPostParameter('returnTo') ?? $this->getRouteVariable('returnTo');
                    $published = $this->publishSyllabus($syllabus, 'private', $published);

                    $this->flash(
                        'Syllabus has been unshared. Only course instructors are able to view it now.', 
                        'warning'
                    );   
                    break;

                case 'toggleshare':
                    $returnTo = "syllabus/$syllabus->id/share";
                    if ($this->request->getPostParameter('shareLinkEnabled') === 'on')
                    {
                        $syllabus->token = $syllabus->generateToken();
                        $syllabus->save();
                        $this->flash('Share link enabled', 'success');
                    }
                    else
                    {
                        $syllabus->token = null;
                        $syllabus->save();
                        $this->flash('Share link disabled', 'info');
                    }
                    break;

                case 'addusers':
                    $adHocUsers = $this->request->getPostParameter('adhocUsers', []);
                    $query = $this->request->getPostParameter('query');
                    $type = $this->request->getPostParameter('type', 'edit');
                    $expiry = $this->request->getPostParameter('expiry');
                    $expiration = null;
                    if ($expiry !== 'never')
                    {
                        try 
                        {
                            $expiration = new DateTime;
                            $expiration->modify('+' . $expiry);
                        }
                        catch (Exception $e)
                        {
                            $expiration = null;
                        }
                    }
                    $accounts = $this->schema('Bss_AuthN_Account');
                    
                    $users = $accounts->find(
                        $accounts->id->inList($adHocUsers)->orIf(
                            $accounts->username->inList($query)
                        )
                    );

                    if (($adHocUsers || $query) && $users)
                    {
                        $role = $this->schema('Syllabus_Syllabus_Role')->createInstance();
                        $role->name = ucfirst($type);
                        $role->description = "{$role->name} access granted on syllabus with id #{$syllabus->id} and title '{$syllabusVersion->title}'";
                        $role->createdDate = new DateTime;
                        $role->expiryDate = $expiration;
                        $role->syllabus_id = $syllabus->id;
                        $role->save();

                        $authZ = $this->getAuthorizationManager();
                        foreach ($users as $user)
                        {
                            switch ($type)
                            {
                                case 'clone':
                                    $authZ->grantPermission($user, 'syllabus view', $role, false);
                                    $authZ->grantPermission($user, 'syllabus clone', $role, false);
                                    break;

                                case 'edit':
                                default:
                                    $authZ->grantPermission($user, 'syllabus view', $role, false);
                                    $authZ->grantPermission($user, 'syllabus edit', $role, false);
                                    break;
                            }
                        }
                        $authZ->updateCache();
                        
                        $this->flash('Success adding new editor', 'success');
                    }
                    else
                    {
                        $this->flash('Invalid username submitted for this editor', 'danger');
                    }
                    $this->response->redirect("syllabus/$syllabus->id/share");
                    break;

                case 'remove':
                    
                    $userId = key($this->getPostCommandData());
                    $roleId = array_shift($this->request->getPostParameter('role')[$userId]);

                    $user = $this->schema('Bss_AuthN_Account')->get($userId);
                    $role = $this->schema('Syllabus_Syllabus_Role')->get($roleId);
                    $role->expiryDate = new DateTime;
                    $role->save();
                    
                    $authZ = $this->getAuthorizationManager();
                    switch (lcfirst($role->name))
                    {
                        case 'clone':
                            $authZ->revokePermission($user, 'syllabus clone', $role);
                            if (!$authZ->hasPermission($user, 'syllabus edit', $syllabus))
                            {
                                $authZ->revokePermission($user, 'syllabus view', $role);
                            }
                            break;

                        case 'edit':
                        default:
                            $authZ->revokePermission($user, 'syllabus edit', $role);
                            if (!$authZ->hasPermission($user, 'syllabus clone', $syllabus))
                            {
                                $authZ->revokePermission($user, 'syllabus view', $role);
                            }
                            break;
                    }
                    $authZ->updateCache();

                    $this->flash("User no longer has {$role->name} access to this syllabus");
                    $this->response->redirect("syllabus/$syllabus->id/share");
                    break;
            }
            $this->response->redirect($returnTo);
        }

        $this->template->adHocRoles = $syllabus->getAdHocRoles();
        $syllabus->viewUrl = $this->baseUrl("syllabus/$syllabus->id/view");
        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->courseInfoSection = $syllabusVersion ? $syllabusVersion->getCourseInfoSection() : null;
        $this->template->published = $published;
        $this->template->shareLevel = $this->getShareLevel($syllabus);
        $this->template->viewUrl = $this->baseUrl("syllabus/$syllabus->id/view");
        $this->template->shareLinkEnabled = $syllabus->token !== null && $syllabus->token !== '';
        $this->template->shareLink = $this->baseUrl("syllabus/$syllabus->id/view?token=$syllabus->token");
        $this->template->returnTo = "syllabus/$syllabus->id/share";
        $this->template->activeStudents = $syllabusVersion ? $syllabusVersion->getActiveStudentsEstimation($this) : 0;
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

        $this->setPageTitle('Delete Syllabus');
        $this->addBreadcrumb($routeBase . 'syllabus/'.$syllabus->id, 'Edit Syllabus');
        $this->addBreadcrumb($routeBase . 'syllabus/'.$syllabus->id.'/delete', 'Delete');

        $routeBase = $routeBase === '' ? 'syllabi' : $routeBase;
        $pathParts = [];
        $pathParts[] = $routeBase;
        $pathParts[] = 'syllabus';
        $routeBase = $this->request->getQueryParameter('return', $routeBase);

        $hasDownstreamSyllabiSection = false;
        if (!$syllabus->file)
        {
            foreach ($syllabusVersion->sectionVersions as $sv)
            {
                // NOTE: Removed for now since query too expensive
                // if ($this->hasDownstreamSection($sv, $syllabus, $viewer))
                if (false)
                {
                    $hasDownstreamSyllabiSection = true;
                    break;
                }
            }            
        }

        if ($this->request->wasPostedByUser())
        {   
            switch ($this->getPostCommand()) 
            {
                case 'deletesyllabus':

                    if ($this->hasPermission('admin') || $this->hasSyllabusPermission($syllabus, $viewer, 'delete'))
                    {
                        if ($syllabus->file)
                        {
                            $courseSection = $this->schema('Syllabus_ClassData_CourseSection')->get($syllabus->course_section_id);
                            $published = $this->schema('Syllabus_Syllabus_PublishedSyllabus');
                            if ($published = $published->findOne($published->syllabus_id->equals($syllabus->id)))
                            {
                                $published->delete();
                            }

                            if ($courseSection->syllabus_id === $syllabus->id)
                            {
                                $courseSection->syllabus_id = null;
                                $courseSection->save();
                            }
                            $syllabus->file->delete();
                            $syllabus->delete();
                        }
                        else
                        {
                            $schema = $this->schema('Syllabus_ClassData_CourseSection');
                            $courseSections = $schema->find(
                                $schema->syllabus_id->equals($syllabus->id)
                            );
                            if ($courseSections)
                            {
                                foreach ($courseSections as $courseSection)
                                {
                                    $courseSection->syllabus_id = null;
                                    $courseSection->save();
                                }
                            }
                            $syllabus->delete();                            
                        }

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
        // $this->template->return = $return;
    }

    public function print ()
    {
        $token = null;
        if (!($token = $this->request->getQueryParameter('token')))
        {
            $viewer = $this->requireLogin();
        }
        $syllabus = $this->requireExists($this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id'));
        $syllabusVersion = $syllabus->latestVersion;

        if (($token !== $syllabus->token) && !$this->hasPermission('admin') && 
            !$this->hasSyllabusPermission($syllabus, $viewer, 'view'))
        {
            $this->accessDenied("You do not have edit access for this syllabus.");
        }
        $this->setPrintTemplate();
        $this->setPageTitle('Print Syllabus');
        $this->addBreadcrumb('syllabi', 'My Syllabi');

        if (!$token)
        {
            // check if this syllabus is from a combined course
            $resolvedSyllabus = $this->resolveCombinedCourseSyllabus($syllabus);

            if ($resolvedSyllabus === null)
            {
                $this->requirePermission('admin');
            }
            elseif ($resolvedSyllabus->id !== $syllabus->id)
            {
                $syllabus = $resolvedSyllabus;
                $this->response->redirect('syllabus/' . $resolvedSyllabus->id . '/print');
            }

            // check if viewing as instructor or student and if it is even associated with a course
            list($type, $courseSection) = $this->getEnrollmentType($syllabus, $viewer);
        }
        elseif ($token !== $syllabus->token && !$this->hasSyllabusPermission($syllabus, $viewer, 'view'))
        {
            $this->accessDenied('Nope');
        }

        if (!$token && $type === 'student' && $courseSection)
        {
            $this->addBreadcrumb('syllabus/'.$syllabus->id.'/view', $courseSection->title);
        }
        else
        {
            if (!$token)
            {
                $this->addBreadcrumb('syllabus/'.$syllabus->id, 'Edit');
            }
            $this->addBreadcrumb('syllabus/'.$syllabus->id.'/view', $syllabusVersion->title);    
        }

        $this->template->token = $token;
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
        if (!($token = $this->request->getQueryParameter('token')))
        {
            $viewer = $this->requireLogin();
        }
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id');
        $url = $this->getApplication()->siteSettings->getProperty('atoffice-api-url');
        
        if (($token !== $syllabus->token) && !$this->hasSyllabusPermission($syllabus, $viewer, 'view'))
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
        $url = $url ?? 'https://atoffice.at.sfsu.edu/api/session';
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
                $filename = $title . '.docx';
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

    public function courseView ()
    {
        $courseid = $this->getRouteVariable('courseid');
        $courseSection = $this->schema('Syllabus_ClassData_CourseSection')->get($courseid);

        if ($courseSection && $courseSection->syllabus && $courseSection->syllabus->inDatasource)
        {
            $this->forward('syllabus/' . $courseSection->syllabus->id . '/view');
        }
        $this->forward('syllabus/notfound', [
            'courseSection' => $courseSection
        ]);
    }

    public function syllabusNotFound ()
    {
        $this->template->courseSection = $this->getRouteVariable('courseSection');
    }

    protected function saveAccessLog ($viewer, $syllabus)
    {

        if ((!$viewer || $syllabus->createdById !== $viewer->id) && !$this->hasPermission('admin'))
        {
            $courseSection = $syllabus->getCourseSection();
            $newLog = $this->schema('Syllabus_Syllabus_AccessLog')->createInstance();
            $newLog->accountId = $viewer ? $viewer->id : null;
            $newLog->courseSectionId = $courseSection ? $courseSection->id : null;
            $newLog->syllabusId = $syllabus->id;
            $newLog->accessDate = new DateTime;
            $newLog->save();
        }
    }

    public function view ()
    { 
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id', ['allowNew' => false]);

        if (!($token = $this->request->getQueryParameter('token')) || !$syllabus->token)
        {
            $viewer = $this->requireLogin();
        }
        else
        {
            $viewer = $this->getAccount() ?? null;
        }

        $tempLinks = $this->schema('Syllabus_Syllabus_TemporaryLink');
        if ($tempLink = $tempLinks->findOne($tempLinks->token->equals($this->request->getQueryParameter('temp'))))
        {
            $syllabus = $tempLink->syllabus;
            $tempLink->delete();
        }

        if ($syllabus->file)
        {
            list($type, $courseSection) = $this->getEnrollmentType($syllabus, $viewer);
            // if teacher, send to upload. if student, send to download
            if ($this->hasPermission('admin') || $type === 'student' || $type === 'instructor' || $tempLink)
            {
                $this->saveAccessLog($viewer, $syllabus);
                $this->response->redirect("files/$syllabus->file_id/download/syllabus");
            }
        }

        if ($appReturn = $this->request->getQueryParameter('appReturn'))
        {
            $_SESSION['appReturn'] = $this->request->getQueryParameter('appReturn');
        }
        if ($appReturn || isset($_SESSION['appReturn']))
        {
            if ($syllabus->hasCourseInformationSection())
            {
                $sessionAppReturn = isset($_SESSION['appReturn']) ? $_SESSION['appReturn'] : '';
                $appReturn = $this->request->getQueryParameter('appReturn', $sessionAppReturn);
            }
        }
        
        $this->setSyllabusTemplate();

        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');
        $sections = $this->schema('Syllabus_Syllabus_Section');
        $sectionVersions = $this->schema('Syllabus_Syllabus_SectionVersion');
        $syllabusVersion = $syllabusVersions->get($this->request->getQueryParameter('v')) ?? $syllabus->latestVersion;

        $viewUrl = "syllabus/$syllabus->id/view";
        $editable = false;
        if (!$token && !$tempLink)
        {
            if (($syllabus->createdById === $viewer->id) || $this->hasSyllabusPermission($syllabus, $viewer, 'edit') || 
                $this->hasPermission('admin'))
            {
                $editable = true;
            }            
        }
        else
        {
            $viewUrl .= "?token=$token";
        }

        $title = ($syllabus->inDatasource ? 'Edit' : 'Create') . ' Syllabus';
        $this->setPageTitle($title);

        $routeBase = $this->getRouteVariable('routeBase', '');
        $organization = $this->getRouteVariable('organization', null);
        if ($organization)
        {
            $this->addBreadcrumb($routeBase, $organization->name . ' Home');
        }
        else
        {
            $this->addBreadcrumb('syllabi', 'My Syllabi');
        }

        $pathParts = [];
        $pathParts[] = $this->getRouteVariable('routeBase');
        $pathParts[] = 'syllabus';

        if (!$token && !$tempLink)
        {
            // check if this syllabus is from a combined course
            $resolvedSyllabus = $this->resolveCombinedCourseSyllabus($syllabus);

            if ($resolvedSyllabus === null && $syllabus->createdById !== $viewer->id && 
                !$this->hasSyllabusPermission($syllabus, $viewer, 'edit'))
            {
                $this->requirePermission('admin');
            }
            elseif ($resolvedSyllabus && $resolvedSyllabus->id !== $syllabus->id)
            {
                $syllabus = $resolvedSyllabus;
                $this->response->redirect('syllabus/' . $resolvedSyllabus->id . '/view');
            }

            // check if viewing as instructor or student and if it is even associated with a course
            list($type, $courseSection) = $this->getEnrollmentType($syllabus, $viewer);
        }
        elseif (!$tempLink && $token !== $syllabus->token && !$this->hasSyllabusPermission($syllabus, $viewer, 'view'))
        {
            $this->accessDenied('Nope');
        }

        if (!$tempLink && !$token && $type === 'student' && $courseSection)
        {
            $this->addBreadcrumb('syllabus/'.$syllabus->id.'/view', $courseSection->title);
        }
        else
        { 
            if (!$token && !$tempLink)
            {
                $this->addBreadcrumb($routeBase.'syllabus/'.$syllabus->id, 'Edit');
            }  
            $this->addBreadcrumb($routeBase.'syllabus/'.$syllabus->id.'/view', $syllabusVersion->title);
            $this->template->instructorView = !$token && $tempLink ? true : false;
            $this->template->canChangeShare = !$token && $tempLink && $syllabus->createdById === $viewer->id;
            $this->template->shareLevel = $syllabus->getShareLevel();
            $syllabus->viewUrl = $this->baseUrl($viewUrl);
        }

        $this->saveAccessLog($viewer, $syllabus);

        $this->template->tempLink = $tempLink;
        $this->template->token = $token;
        $this->template->returnTo = $viewUrl;
        $this->template->editable = $editable;
        $this->template->title = $title;
        $this->template->syllabus = $syllabus;
        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $syllabusVersion->getSectionVersionsWithExt(true);
        $this->template->organization = $organization;
        $this->template->appReturn = $appReturn;
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

    /**
     * Bump syllabus and section versions--creating new instances of generic, real, and subsections.
     */  
    protected function saveSyllabus ($syllabus, $paramData=null, $updateScreenshot=true)
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

        if ($syllabus->file)
        {
            $syllabus->file->delete();
        }

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
        // if ($anyChange && $updateScreenshot)
        // {
        //     $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
        //     $this->getScreenshotUrl($syllabus->id, $screenshotter, false);            
        // }

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
        $this->requirePermission('syllabus edit');

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
    private function updateCourseSyllabus ($fromSyllabus, $toSyllabus, $cdCourseSection, $latestVersion=null)
    {
        $syllabusVersion = $latestVersion ?? $toSyllabus->latestVersion;
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
        $toSyllabus->course_section_id = $cdCourseSection->id;
        $toSyllabus->save();

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
        
        if ($syllabus->templateAuthorizationId && !$this->hasPermission('admin'))
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
        else
        {
            // type is instructor if user is an enrolled instructor and student if user is enrolled student
            list($type, $courseSection) = $this->getEnrollmentType($syllabus, $user);
            $authZ = $this->getAuthorizationManager();
            $hasPermission = false;
            switch ($permission)
            {
                case 'delete':
                    $this->requirePermission('syllabus delete');
                    $hasPermission = $syllabus->createdById === $user->id;
                    break;
                case 'edit':
                    $hasPermission = $authZ->hasPermission($user, 'syllabus edit', $syllabus);
                    $hasPermission = $hasPermission || 
                        ($courseSection && $type !== '' || $syllabus->createdById === $user->id);
                    break;
                case 'view':
                    $hasPermission = $authZ->hasPermission($user, 'syllabus view', $syllabus);
                    $hasPermission = $hasPermission || 
                        ($courseSection && $type !== '' || $syllabus->createdById === $user->id);
                    break;
                case 'clone':
                    $hasPermission = $authZ->hasPermission($user, 'syllabus clone', $syllabus);
                    $hasPermission = $hasPermission || 
                        ($courseSection && $type !== 'instructor' || $syllabus->createdById === $user->id);
                    break;
                case 'share':
                    $hasPermission = $authZ->hasPermission($user, 'syllabus share', $syllabus);
                    $hasPermission = $hasPermission || 
                        ($courseSection && $type !== '' || $syllabus->createdById === $user->id);
                    break;
                case 'list':
                default:
                    $this->requirePermission('syllabus list');
                    $hasPermission = $type !== '';
                    break;
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

    public function outcomesLookup ()
    {
        $this->requireLogin();
        if ($sectionId = $this->request->getQueryParameter('section'))
        {
            $service = new Syllabus_ClassData_Service($this->getApplication());
            list($code, $data) = $service->getSectionOutcomes($sectionId);

            if ($code === 200 && isset($data['outcomes']))
            {
                $results = [
                    'message' => 'Outcomes found.',
                    'status' => 'success',
                    'data' => $data['outcomes']
                ];
                $results['data']['external_key'] = $sectionId;
            }
            else
            {
                $results = [
                    'message' => 'No outcomes found for this course.',
                    'status' => 'error',
                    'data' => ''
                ];
            }

            echo json_encode($results);
            exit;
        }
    }

    // todo: add 'section ##' search
    // todo: add department joins
    private function searchSyllabi ($searchQuery, $account, $options=[])
    {
        $result = [];
        $rs = '';
        $searchQuery = strtolower(strip_tags($searchQuery));
        $searchWords = explode(' ', $searchQuery);
        $query = 
        "
            select distinct s.id from syllabus_syllabus s 
            inner join syllabus_syllabus_versions sv 
                on (sv.syllabus_id = s.id)
            inner join syllabus_syllabus_version_section_version_map svsv
                on (sv.id = svsv.syllabus_version_id)
            left join syllabus_classdata_course_sections cdcs 
                on (sv.syllabus_id = cdcs.syllabus_id)
            left join syllabus_departments d 
                on (cdcs.department_id = d.id)
            left join syllabus_colleges c 
                on (d.college_id = c.id)
            where 
                (
                    LOWER(sv.title) like '%{$searchQuery}%' or 
                    LOWER(sv.description) like '%{$searchQuery}%' or 
                    LOWER(cdcs.title) like '%{$searchQuery}%' or 
                    LOWER(cdcs.class_number) like '%{$searchQuery}%' or 
                    LOWER(cdcs.year) like '%{$searchQuery}%' or 
                    LOWER(d.name) like '%{$searchQuery}%' or 
                    LOWER(d.abbreviation) like '%{$searchQuery}%' or 
                    LOWER(c.name) like '%{$searchQuery}%' or 
                    LOWER(c.abbreviation) like '%{$searchQuery}%'
                ) and 
                s.created_by_id = {$account->id}";
        if ($searchQuery && $account)
        {
            $rs = pg_query($query);

            while (($row = pg_fetch_row($rs)))
            {
                $result[] = $row[0];
            }          
        }

        $singleResults = [];
        if (count($searchWords) > 1)
        {
            foreach ($searchWords as $word)
            {
                $searchQuery = $word;
                $rs = pg_query($query);
                while (($row = pg_fetch_row($rs)))
                {
                    $singleResults[] = $row[0];
                }  
            }
            $result = array_unique(array_merge($result, $singleResults));
        }

        $schema = $this->schema('Syllabus_Syllabus_Syllabus');
        
        return $schema->find($schema->id->inList($result), $options);
    }

   /**
     *
     * This will determine if a user is able to view a syllabus if they're in a combined iLearn course
     * Returns are prioritized like so:
     *  -   The original syllabus if a combined course exists that has no syllabus of its own
     *  -   The combined course syllabus if exists
     *  -   Null if no combined courses exist
     */
    public function resolveCombinedCourseSyllabus ($syllabus)
    {
        $result = null;

        $viewer = $this->requireLogin();
        $service = new Syllabus_ClassData_Service($this->getApplication());

        // if user is enrolled in syllabus' course, show it to them and do nothing else.
        $courseSection = null;
        if ($syllabus->latestVersion)
        {
            if ($sectionVersion = $syllabus->latestVersion->getCourseInfoSection())
            {
                if ($courseSection = $sectionVersion->resolveSection()->classDataCourseSection)
                {
                    if ($courseSection->enrollments->has($viewer->classDataUser))
                    {
                        // they're enrolled in this syllabus' course
                        $result = $syllabus;
                    }
                }
            }                  
        }

        if (!$result && $courseSection)
        {
            list($code, $enrollments) = $service->getUserEnrollments($viewer->username, $courseSection->getTerm(true), 'student');
            if ($code === 200 && !empty($enrollments))
            {
                list($code, $combines) = $service->getChannelInfo('ilearn', $courseSection->id);
                if ($code === 200 && !empty($combines))
                {
                    foreach ($enrollments['courses'] as $course)
                    {
                        if (in_array($course['id'], $combines['combines']))
                        {
                            $combinedCourseSection = $this->schema('Syllabus_ClassData_CourseSection')->get($course['id']);
                            if ($combinedCourseSection->syllabus)
                            {
                                $result = $combinedCourseSection->syllabus;
                                break;
                            }
                            else
                            {
                                $result = $syllabus;
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    // type is instructor if user is an enrolled instructor and student if user is enrolled student
    protected function getEnrollmentType ($syllabus, $viewer)
    {
        $courseSection = null;
        $type = '';
        
        if ($syllabus->file && $syllabus->courseSection)
        {
            $courseSection = $syllabus->courseSection;
        }
        elseif ($sectionVersion = $syllabus->latestVersion->getCourseInfoSection())
        {
            $courseSection = $sectionVersion->resolveSection()->classDataCourseSection;
        }

        if ($courseSection && $courseSection->enrollments->has($viewer->classDataUser))
        {
            $type = $courseSection->enrollments->getProperty($viewer->classDataUser, 'role');
            // $type = 'instructor';
            // $roles = $this->schema('Syllabus_AuthN_Role');
            // $studentRole = $roles->findOne($roles->name->equals('Student'));
            // if ($viewer->roles->has($studentRole))
            // {
            //     $type = 'student';
            // }                    
        }

        return [$type, $courseSection];
    }

    public function ping ()
    {   
        $courseSection = $this->helper('activeRecord')->fromRoute('Syllabus_ClassData_CourseSection', 'id');
        $returnArray = [];
        $returnArray['published'] = false;
        if ($courseSection && $courseSection->syllabus) 
        {
            if ($courseSection->syllabus->getShareLevel() === 'all')
            {
                $returnArray['exists'] = true;
                $returnArray['url'] = $this->baseUrl('syllabus/' . $courseSection->id . '/view');
                $returnArray['edited'] = true;
                $returnArray['visible'] = true;
                $returnArray['published'] = true;
            }
            else
            {
                $returnArray['sid'] = $courseSection->syllabus->id;
            }
        } 
        else 
        {
            $returnArray['exists'] = false;
        }

        $return_json = json_encode($returnArray);
        echo($return_json);
        exit;
    }

    public function thumbInfo ()
    {
        // $syllabusId = $this->getRouteVariable('id');
        // $syllabus = $this->schema('Syllabus_Syllabus_Syllabus')->get($syllabusId);

        // $screenshotter = new Syllabus_Services_Screenshotter($this->getApplication());
        // $screenshotter->saveUids(sha1($syllabusId), $syllabusId);

        // $urls = [$syllabusId => $this->baseUrl("syllabus/{$syllabusId}/screenshot")];
        // $responseData = $screenshotter->concurrentRequests($urls, true, sha1($syllabusId));
        // $data = json_decode($responseData);

        // $results = [
        //     'message' => 'Accepted & Processing',
        //     'status' => 'success',
        //     'success' => true,
        //     'data' => $data,
        //     'imageSrc' => $data->imageUrls->$syllabusId,
        //     'syllabusId' => $syllabusId
        // ];

        $results = [
            'message' => '',
            'success' => false,
            'status' => 404
        ];
        
        echo json_encode($results);
        exit;
    }

    public function screenshot ()
    {
        $sid = $this->getRouteVariable('id');
        $keyPrefix = sha1($sid) . '-';
        $key = "{$keyPrefix}{$sid}";
        $accessToken = Syllabus_Services_Screenshotter::CutUid($key);
        $tokenHeader = $this->request->getHeader('X-Custom-Header');

        // if (!$tokenHeader && !$accessToken && ($tokenHeader !== $accessToken))
        if (!$tokenHeader && !$accessToken)
        {
            $this->accessDenied('No access token available in this request.');
        }

        $this->setScreenshotTemplate();

        $syllabusVersions = $this->schema('Syllabus_Syllabus_SyllabusVersion');   
        $syllabus = $this->helper('activeRecord')->fromRoute('Syllabus_Syllabus_Syllabus', 'id');
        $syllabusVersion = $syllabus->latestVersion;

        $this->template->syllabusVersion = $syllabusVersion;
        $this->template->sectionVersions = $syllabusVersion ? $syllabusVersion->getSectionVersionsWithExt(true) : null;
    }

    public function asyncSubmit ()
    {
        if ($this->request->wasPostedByUser())
        {
            $results = [
                'message' => 'Accepted & Processing',
                'status' => 'pending',
                'success' => false
            ];

            $syllabusId = $this->getRouteVariable('id');
            $syllabus = $this->schema('Syllabus_Syllabus_Syllabus')->get($syllabusId);

            $data = $this->request->getPostParameters();
            list($success, $newSyllabusVersion) = $this->saveSyllabus($syllabus, $data, false);

            $results = [
                'status' => 'success',
                'success' => true,
            ];

            echo json_encode($results);
            exit;
        }       
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
            $failure = $file->createFromRequest(
                $this->request, 'file', false, 'application/octet-stream,text/plain'
            );
            
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



        // $this->getScreenshotUrl($syllabus->id);

        if (!$success)
        {
            $syllabusVersion->delete();
            $syllabus->delete();
            return null;
        }

        return [$errors, $syllabusVersion];
    }

    public function autocompleteAccounts ()
    {
        $role = $this->request->getQueryParameter('role');
        $roleRestrict = null;

        if ($role)
        {
            $roleRestrict = $this->schema('Syllabus_AuthN_Role')->get($role);
        }

        $roles = $this->schema('Syllabus_AuthN_Role');
        $adminRole = $roles->findOne($roles->name->equals('Administrator'));

        $query = $this->request->getQueryParameter('s');
        $queryParts = explode(' ', $query);

        $accounts = $this->schema('Bss_AuthN_Account');

        $conds = array();

        foreach ($queryParts as $part)
        {
            $search = '%' . $part . '%';
            $conds[] = $accounts->anyTrue(
                $accounts->firstName->lower()->like(strtolower($search)),
                $accounts->lastName->lower()->like(strtolower($search)),
                $accounts->emailAddress->lower()->like(strtolower($search)),
                $accounts->username->lower()->like(strtolower($search))
            );
        }

        $candidates = array();

        if (!empty($conds))
        {
            $cond = array_shift($conds);

            foreach ($conds as $c)
            {
                $cond = $cond->orIf($c);
            }

            $candidates = $accounts->find($cond, array('orderBy' => array('+lastName', '+firstName'), 'arrayKey' => 'username'));

            $authZ = $this->getAuthorizationManager();
            foreach ($candidates as $i => $candidate)
            {
                if ($candidate->roles->has($adminRole) || $authZ->hasPermission($candidate, 'admin') || 
                    strlen($candidate->username) !== 9)
                {
                    unset($candidates[$i]);
                }
            }
        }

        if ($candidates)
        {
            $options = array();
            foreach ($candidates as $candidate)
            {
                $options[$candidate->id] = array(
                    'id' => $candidate->id,
                    'firstName' => $candidate->firstName,
                    'lastName' => $candidate->lastName,
                    'username' => $candidate->username,
                );
            }

            $results = array(
                'message' => 'Candidates found.',
                'status' => 'success',
                'data' => $options
            );
        }
        else
        {
            $results = array(
                'message' => 'No candidates found.',
                'status' => 'error',
                'data' => ''
            );
        }

        echo json_encode($results);
        exit;
    }
}