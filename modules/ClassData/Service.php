<?php

/**
 * The service functionality to connect to ClassData/SIS data.
 *
 * @author someone at AT
 * @author Steve Pedersen (pedersen@sfsu.edu)
 */
class Syllabus_ClassData_Service
{

    private $urlBase;
    private $apiKey;
    private $apiSecret;
    private $channel;
    private $dataSource;
    private $sims_service;
    private $r;
    private $activeSemester;
    private $allDepartments;
    private $allCourses;

    public function __construct($app, $channel='raw')
    {
        $siteSettings = $app->siteSettings;
        $this->application = $app;
        $this->urlBase = $siteSettings->getProperty('classdata-api-url') ?? 'http://classdata.dev.at.sfsu.edu/'; // TODO: remove these hardcoded values
        $this->apiKey = $siteSettings->getProperty('classdata-api-key') ?? 'ca1a3f6f-7cac-4e52-9a0a-5cbf82b16bc9';
        $this->apiSecret = $siteSettings->getProperty('classdata-api-secret') ?? '4af2614e-142d-4db8-8512-b3ba13dd0143';
        $this->channel = $channel;
    }
    
    protected function signResource ($resource, $paramMap)
    {
        $url = $this->urlBase . $resource;

        $paramMap['a'] = $this->apiKey;
        $paramMap['channel'] = (!isset($paramMap['channel']) ? $this->channel : $paramMap['channel']);
        uksort($paramMap, 'strcmp');
        
        $params = [];
        foreach ($paramMap as $k => $v) { $params[] = urlencode($k) . '=' . urlencode($v); }
        $url .= '?' . implode('&', $params);
        
        return $url . '&s=' . sha1($this->apiSecret . $url);
    }
    
    // NOTE: This function doesn't seem to be working. The API freezes up
    public function getEnrollments ($semester, $role = null)
    {
        $paramMap = [];
        
        if ($role)
        {
            $paramMap['role'] = $role;
        }
        
        $url = $this->signResource("enrollments/{$semester}", $paramMap);
        list($code, $data) = $this->request($url);

        return [$code, $data];
    }

    public function getUserEnrollments ($userid, $semester, $role = null)
    {
        $paramMap = [];
        if ($role)
        {
            $paramMap['role'] = $role;
        }

        $url = $this->signResource("users/{$userid}/semester/{$semester}", $paramMap);
        list($code, $data) = $this->request($url);

        return [$code, $data];
    }
    
    public function getChanges ($semester, $since)
    {
        $url = $this->signResource("changes/{$semester}", array('since' => $since));
        list($code, $data) = $this->request($url);

        return [$code, $data];
    }

    public function getCourseSection ($id)
    {
        $url = $this->signResource('courses/' . $id, array('include' => 'description,prerequisites,students,instructors,userdata'));
        list($code, $data) = $this->request($url);

        return [$code, $data];
    }
    
    // TODO: POST needs testing of implementation
    public function getCourseSections ($idList)
    {
        $url = $this->signResource('courses', array('include' => 'description,prerequisites'));
        list($code, $data) = $this->request($url, true, array('ids' => implode(',', $idList)));

        return [$code, $data];
    }
    
    // TODO: POST needs testing of implementation
    public function getUsers ($idList)
    {
        $url = $this->signResource('users', array('include' => 'description,prerequisites'));
        list($code, $data) = $this->request($url, true, array('ids' => implode(',', $idList)));

        return [$code, $data];
    }

    public function getOrganizations ()
    {
        $paramMap = array('include' => 'college');
        $url = $this->signResource('organizations', $paramMap);
        list($code, $data) = $this->request($url);

        return [$code, $data];
    }

    public function getDepartments ()
    {
        $paramMap = [];
        $url = $this->signResource('departments', $paramMap);
        list($code, $data) = $this->request($url);

        return [$code, $data];
    }

    // TODO: POST needs testing of implementation
    protected function request ($url, $post=false, $postData=[])
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
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
        } 
        $rawData = curl_exec($ch);
        
        if (!curl_error($ch)) {
            $data = json_decode($rawData, true);
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        
        return [$httpCode, $data];
    }

    // TODO: implement transaction
    public function importOrganizations ()
    {
        list($code, $data) = $this->getOrganizations();

        if ($code === 200)
        {
            $result = [];
            foreach ($data['organizations'] as $deptKey => $org)
            {
                $college = key(array_flip($org['college']));
                if (!array_key_exists($college, $result))
                {
                    $result[$college]['departments'] = [];
                }
                $result[$college]['departments'][$deptKey] = $org['name'];
            }

            $colleges = $this->getSchema('Syllabus_AcademicOrganizations_College');
            $departments = $this->getSchema('Syllabus_AcademicOrganizations_Department');

            $allColleges = $colleges->findValues(['name' => 'id']);
            $allDepartments = $departments->findValues(['name' => 'id']);

            foreach ($result as $collegeName => $departmentList)
            {
                if (!isset($allColleges[$collegeName]))
                {
                    $college = $colleges->createInstance();
                    $college->createdDate = new DateTime;
                    $college->name = $collegeName;
                    $college->save();
                    $allColleges[$collegeName] = $college->id;
                }

                foreach ($departmentList['departments'] as $id => $departmentName)
                {
                    if (!isset($allDepartments[$departmentName]))
                    {
                        $department = $departments->createInstance();
                        $department->createdDate = new DateTime;
                        $department->name = $departmentName;
                        $department->college_id = $allColleges[$collegeName] ?? $college->id ?? null;
                        $department->externalKey = $id;
                        $department->abbreviation = $id;
                        $department->save();
                        $allDepartments[$departmentName] = $department->id;
                    }
                }
            }
        }
    }

    public function import ($semesterCode)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
       
        $courses = $this->getSchema('Syllabus_ClassData_CourseSection');
        $users = $this->getSchema('Syllabus_ClassData_User');
        $enrollments = $this->getSchema('Syllabus_ClassData_Enrollment');
        $this->allDepartments = $this->getSchema('Syllabus_AcademicOrganizations_Department')->findValues(['externalKey' => 'id']);
        $this->allCourses = $this->getSchema('Syllabus_ClassData_Course')->findValues(['id' => 'id']);
        
        $dataSource = $courses->getDefaultDataSource();
        $tx = $dataSource->createTransaction();
        $now = new DateTime;
        
        // TODO: Look into beefing up the logging system here
        // $since field can simply be a site setting.
        $logs = $this->getSchema('Syllabus_ClassData_SyncLog');
        $lastLog = $logs->findOne($logs->status->equals(200), ['orderBy' => ['-dt', '-id']]);
        $newLog = $logs->createInstance();
        
        if ($lastLog === null)
        {
            $since = '1970-01-01';
        }
        else
        {
            $since = $lastLog->dt->format('c');
        } 

        list($status, $data) = $this->getChanges($semesterCode, $since);

        if ($status != 200)
        {
            if ($data && isset($data['error']))
            {
                $newLog->errorCode = $data['error'];
                $newLog->errorMessage = $data['message'];
            }
            else
            {
                $newLog->errorCode = 'NoErrorResource';
                $newLog->errorMessage = 'The response contained an error code, but the body was not a JSON-formatted error document.';
            }
        }
        else
        {
            // Keeps track of existing courses and users as we process the batches.
            $existingCourseSet = $courses->findValues(['id' => 'id']);
            $existingUserSet = $users->findValues(['id' => 'id']);
            
            // Process the courses in batches.
            
            foreach ($this->batches($data['courses'], 1000) as $batch)
            {
                foreach ($batch as $courseId => $actionList)
                {
                    foreach ($actionList as $action)
                    {
                        if (array_key_exists($courseId, $existingCourseSet))
                        {
                            if ($action['t'] == '+' && $existingCourseSet[$courseId])
                            {
                                // If we're trying to add a course that was 
                                // previously marked as deleted, remove all of its
                                // old enrollments. (We kept them before so that we
                                // had a record of the course's instructors. But
                                // now we're expecting new enrollments for the
                                // course -- which might replicate the info we
                                // saved.)
                                
                                // $this->deleteCourseEnrollments($tx, $enrollments, $courseId);
                            }
                            
                            if ($action['t'] == '+' || $action['t'] == '!')
                            {
                                $this->updateCourseSection($tx, $now, $courses, $courseId, $action['d']);
                            }
                            elseif ($action['t'] == '-')
                            {
                                $this->dropCourseSection($tx, $now, $courses, $courseId);
                                $existingCourseSet[$courseId] = true; // Mark as deleted.
                            }
                        }
                        elseif ($action['t'] == '+' || $action['t'] == '!')
                        {
                            $this->addCourseSection($tx, $now, $courses, $courseId, $action['d']);
                            $existingCourseSet[$courseId] = false;
                        }
                    }
                }
            }
            
            foreach ($this->batches($data['users'], 1000) as $idx => $batch)
            {
                foreach ($batch as $userId => $actionList)
                {
                    foreach ($actionList as $action)
                    {
                        if (array_key_exists($userId, $existingUserSet))
                        {
                            switch ($action['t'])
                            {
                                case '+':
                                case '!':
                                    $this->updateUser($tx, $now, $users, $userId, $action['d']);
                                    break;
                                case '-':
                                    $this->dropUser($tx, $now, $users, $userId);
                                    unset($existingUserSet[(string)$userId]);
                                    break;
                            }
                        }
                        elseif ($action['t'] == '+' || $action['t'] == '!')
                        {
                            $this->addUser($tx, $now, $users, $userId, $action['d']);
                            $existingUserSet[(string)$userId] = true;
                        }
                    }
                }
            }

            $existingEnrollmentSet = $this->loadExistingEnrollments($dataSource, $data['enrollments'], $existingCourseSet, $existingUserSet);
            
            // Enrollments.
            foreach ($data['enrollments'] as $courseId => $courseEnrollList)
            {
                if (array_key_exists($courseId, $existingCourseSet))
                {
                    foreach ($courseEnrollList as $action)
                    {
                        $role = ($action[1] == 's' ? 'student' : 'instructor');
                        $userId = substr($action, 2);
                        
                        if (isset($existingUserSet[(string)$userId]))
                        {
                            switch ($action[0])
                            {
                                case '+':
                                    if (!isset($existingEnrollmentSet[$courseId]) || !isset($existingEnrollmentSet[$courseId][$userId]))
                                    {
                                        $this->addEnrollment($tx, $now, $enrollments, $userId, $courseId, $role, $semesterCode);
                                    }
                                    break;
                                case '-':
                                    $this->dropEnrollment($tx, $now, $enrollments, $userId, $courseId, $role);
                                    break;
                            }
                        }
                        else
                        {
                            $this->getApplication()->log('debug', "Enrollment {$action} in {$courseId} for non-existent user: {$userId}");
                        }
                    }
                }
                else
                {
                    $this->getApplication()->log('debug', "Enrollment for non-existent course: {$courseId}");
                }
            }
        }
        
        $newLog->dt = $now;
        $newLog->status = $status;
        $newLog->save();
        
        $tx->commit();
        return $now;
    }

    protected function batches ($data, $entries)
    {
        $count = count($data);
        $batches = [];
        
        for ($i = 0; $i < $count; $i += $entries)
        {
            $batches[] = array_slice($data, $i, $entries, true);
        }
        
        return $batches;
    }

    /* Flags the user as being deleted but doesnt actually delete from the dB*/
    protected function dropUser ($tx, $now, $users, $userId)
    {
        $users->update(
            [
                'deleted' => true,
                'modifiedDate' => $now,
            ],
            $users->id->equals($userId)
        );
    }

    // add the create and modify fields
    public function addUser ($tx, $now, $users, $userId, $data)
    {
        $users->insert(
            [
                'id' => $userId,
                'firstName' => $data['first'] ?? '',
                'lastName' => $data['last'] ?? '',
                'emailAddress' => $data['mail'] ?? '',
                'createdDate' => $now,
                'modifiedDate' => $now,
                'deleted' => false,
            ],
            ['transaction' => $tx]
        );
    } 

    protected function updateUser ($tx, $now, $users, $userId, $data)
    {
        $users->update(
            [
                'firstName' => $data['first'],
                'lastName' => $data['last'],
                'emailAddress' => $data['mail'],
                'modifiedDate' => $now,
            ],
            $users->id->equals($userId),
            ['transaction' => $tx]
        );
    }

    public function addEnrollment ($tx, $now, $enrollments, $userId, $courseId, $role, $ysem)
    {
        $enrollments->insert(
            [
                'courseSectionId' => $courseId,
                'userId' => $userId,
                'role' => $role,
                'yearSemester' => $ysem,
                'createdDate' => $now,
                'modifiedDate' => $now,
                'deleted' => false,
            ],
            ['transaction' => $tx]
        );
    } 

    protected function dropEnrollment ($tx, $now, $courses, $userId, $courseId, $role)
    {
        $ref = $courses->enrollments;
        $dataSource = $courses->getDefaultDataSource();
        
        $deleteQuery = $tx->createDeleteQuery($ref->getVia());
        $deleteQuery->setCondition($dataSource->andConditions([
            $dataSource->createCondition(
                $dataSource->createSymbol('course_section_id'),
                Bss_DataSource_Condition::OP_EQUALS,
                $dataSource->createTypedValue($courseId, 'string')
            ),
            $dataSource->createCondition(
                $dataSource->createSymbol('user_id'),
                Bss_DataSource_Condition::OP_EQUALS,
                $dataSource->createTypedValue($userId, 'string')
            ),
        ]));
        $deleteQuery->execute();
    }

    /**
     * Flags a course as having been dropped. We actually keep it around in the
     * cache because we might have resources associated with it in DIVA and we
     * want to keep that stuff.
     */
    protected function dropCourseSection ($tx, $now, $courses, $courseId)
    {
        $courses->update(
            [
                'deleted' => true,
                'modifiedDate' => $now,
            ],
            $courses->id->equals($courseId)
        );
    }

    /**
     * Add a course to the cache. add prereq
     */
    public function addCourseSection ($tx, $now, $courses, $courseId, $data)
    {
        $num='-1';
        $sem='-1';
        $year='-1';
        $sec='-1';
        if (empty($data['sn']) || empty($data['title']))
        {
            $missing = [];
            if (empty($data['sn'])) $missing[] = 'sn';
            if (empty($data['title'])) $missing[] = 'title';
            $this->application->log('warning', "Skipping add for course {$courseId}: Missing required field" . (count($missing) > 1 ? 's' : '') . ': ' . implode(', ', $missing));
            return;
        }
        //Parse the Course Section
        $sec = preg_split('[-]', $data['sn']);
        $sec = $sec[2];
        //Parse the semester from the short name
        if (strpos($data['sn'],'Fall') !== false) {
            $sem='7';
        }elseif (strpos($data['sn'],'Winter') !== false) {
            $sem='1';
        }elseif (strpos($data['sn'],'Spring') !== false) {
            $sem='3';
        }else{
            $sem='5';
        }
        $year = preg_split('[-]', $data['sn']);
        $year = $year[4];

        $num = preg_split('[-]', $data['sn']);
        $num = $num[0] . ' ' . $num[1];
        
        if (!isset($this->allCourses[$data['course']]))
        {
            $course = $this->getSchema('Syllabus_ClassData_Course')->createInstance();
            $course->id = $data['course'];
            $course->createdDate = new DateTime;
            $course->modifiedDate = new DateTime;
            $course->deleted = false;
            $course->department_id = $this->allDepartments[$data['department']] ?? '';
            $course->save($tx);
            $this->allCourses[$data['course']] = $course->id;
        }

        $courses->insert(
            [
                'id' => $courseId,
                'title' => $data['title'],
                'classNumber'=> $num,
                'year' => $year,
                'semester' => $sem,
                'sectionNumber' => $sec,
                'description' => (isset($data['desc']) ? $data['desc'] : ''),
                'createdDate' => $now,
                'modifiedDate' => $now,
                'deleted' => false,
                'department_id' => $this->allDepartments[$data['department']] ?? '',
                'course_id' => $this->allCourses[$data['course']],
            ],
            ['transaction' => $tx]
        );
    } 
    
    protected function updateCourseSection ($tx, $now, $courses, $courseId, $data)
    {
        if (empty($data['sn']) || empty($data['title']))
        {
            $missing = [];
            if (empty($data['sn'])) $missing[] = 'sn';
            if (empty($data['title'])) $missing[] = 'title';
            $this->application->log('warning', "Skipping update for course {$courseId}: Missing required field" . (count($missing) > 1 ? 's' : '') . ': ' . implode(', ', $missing));
            return;
        }

        //Parse the Course Section
        $sec = preg_split('[-]', $data['sn']);
        $sec = $sec[2];
        //Parse the semester from the short name
        if (strpos($data['sn'],'Fall') !== false) {
            $sem='7';
        }elseif (strpos($data['sn'],'Winter') !== false) {
            $sem='1';
        }elseif (strpos($data['sn'],'Spring') !== false) {
            $sem='3';
        }else{
            $sem='5';
        }
        $year = preg_split('[-]', $data['sn']);
        $year = $year[4];

        $num = preg_split('[-]', $data['sn']);
        $num = $num[0] . ' ' . $num[1];

        $courses->update(
            [
                'id' => $courseId,
                'title' => $data['title'],
                'classNumber'=> $num,
                'year' => $year,
                'semester' => $sem,
                'sectionNumber' => $sec,
                'description' => (isset($data['desc']) ? $data['desc'] : ''),
                'createdDate' => $now,
                'modifiedDate' => $now,
                'deleted' => false,
                'department_id' => $this->allDepartments[$data['department']] ?? '',
            ],
            $courses->id->equals($courseId),
            ['transaction' => $tx]
        );
    } 

    protected function loadExistingEnrollments ($dataSource, $enrollments, $existingCourseSet, $existingUserSet)
    {
        $enrollCourseSet = [];
        $enrollUserSet = [];
        
        foreach ($enrollments as $courseId => $courseEnrollList)
        {
            if (array_key_exists($courseId, $existingCourseSet))
            {
                $enrollCourseSet[$courseId] = true;
                foreach ($courseEnrollList as $action)
                {
                    $userId = substr($action, (($action[0] === '+' || $action[0] === '-') ? 2 : 1));
                    $enrollUserSet[$userId] = true;
                }
            }
        }
        
        // There are no enrollments to check?
        if (empty($enrollCourseSet) || empty($enrollUserSet))
        {
            return [];
        }
        
        $existingEnrollmentSet = [];
        
        $query = $dataSource->createSelectQuery('syllabus_classdata_enrollments');
        $query->project('course_section_id');
        $query->project('user_id');
        $query->setCondition($dataSource->andConditions([
            $dataSource->createCondition(
                $dataSource->createSymbol('course_section_id'),
                Bss_DataSource_Condition::OP_IN,
                $dataSource->createTypedValue(array_keys($enrollCourseSet), 'string')
            ),
            $dataSource->createCondition(
                $dataSource->createSymbol('user_id'),
                Bss_DataSource_Condition::OP_IN,
                $dataSource->createTypedValue(array_keys($enrollUserSet), 'string')
            ),
        ]));
        $query->orderBy('course_section_id', SORT_ASC);
        $query->orderBy('user_id', SORT_ASC);
        $rs = $query->execute();
        
        while ($rs->next())
        {
            $courseId = $rs->getValue('course_section_id', 'string');
            $userId = $rs->getValue('user_id', 'string');
            
            if (!isset($existingEnrollmentSet[$courseId]))
            {
                $existingEnrollmentSet[$courseId] = [];
            }
            
            $existingEnrollmentSet[$courseId][$userId] = true;
        } 
        return $existingEnrollmentSet;
    }

    public function getSchema ($name)
    {
        return $this->application->schemaManager->getSchema($name);
    }
}