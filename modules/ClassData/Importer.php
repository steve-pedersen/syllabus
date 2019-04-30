<?php

/**
 * Import data from SIMS.
 *
 * @author      Daniel A. Koepke (dkoepke@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_ClassData_Importer
{
    const DEFAULT_DATASOURCE_ALIAS = 'default';
    const DATASOURCE_ALIAS = 'classdata';
    const COURSES = 'syllabus_classdata_courses';
    const USERS = 'syllabus_classdata_users';
    const ENROLLMENTS = 'syllabus_classdata_enrollments';
   

    const API_ENDPOINT = 'https://classdata.sfsu.edu/';
    const API_KEY = 'ca1a3f6f-7cac-4e52-9a0a-5cbf82b16bc9';
    const API_SECRET = '4af2614e-142d-4db8-8512-b3ba13dd0143';
    private $dataSource;
    private $sims_service;
    private $r;
    private $activeSemester;

    private $defaults = array();
    
    
    /**
     */
    public function __construct (Bss_Core_Application $app, $dataSource = null)
    {
        $this->application = $app;
        $this->dataSource = ($dataSource ? $dataSource : $app->dataSourceManager->getDataSource(self::DEFAULT_DATASOURCE_ALIAS));

    }
    
    /**
     */
    public function getDataSource ()
    {
        return $this->dataSource;
    }

    public function getDefault($type, $defaultValue = null)
    {
        $default = $defaultValue;

        if (isset($this->defaults[$type]))
        {
            $default = $this->defaults[$type];
        }
        else
        {
           $internalName = 'classdata-default-' . $type;
           $default = $this->application->siteSettings->getProperty($internalName);

           if ($default)
           {
                $this->defaults[$type] = $default;
           }
        }

        return $default;
    }

    protected function signResource ($resource, $paramMap)
    {
        $url = self::API_ENDPOINT . $resource;
        $paramMap['a'] = self::API_KEY;
        uksort($paramMap, 'strcmp');
        
        $params = array();
        foreach ($paramMap as $k => $v) { $params[] = urlencode($k) . '=' . urlencode($v); }
        $url .= '?' . implode('&', $params);
        
        return $url . '&s=' . sha1(self::API_SECRET . $url);
    }

    public function getCourses ($idList)
    {
        $url = $this->signResource('courses', array('include' => 'description,prerequisites'));
        list($code, $data) = $this->request($url, true, array('ids' => implode(',', $idList)));
        
        if (!empty($data) && $code === 200)
        {
            return $data['courses'];
        }
        
        return false;
    }

    public function getUsers ($idList)
    {
        $url = $this->signResource('users', array('include' => 'description,prerequisites'));
        list($code, $data) = $this->request($url, true, array('ids' => implode(',', $idList)));
        
        if (!empty($data) && $code === 200)
        {
            return $data['users'];
        }
        
        return false;
    }    

    protected function getEnrollments ($semester, $role = null)
    {
        
        $paramMap = array('ids' => true);
        
        if ($role)
        {
            $paramMap['role'] = $role;
        }
        
        $url = $this->signResource("enrollments/{$semester}", $paramMap);

        return $this->request($url);
    }

    /* Send request to Class Data to get data for the users,enrollments, and syllabi for each active semester*/
    public function getChanges ($semester, $since)
    {
        error_reporting(E_ALL);
        $url = $this->signResource("changes/{$semester}", array('since' => $since));

        return $this->request($url);
    }

    // TODO: POST needs testing of implementation
    protected function request ($url, $post=false, $postData=array())
    {
        ini_set('memory_limit', '-1');
        @set_time_limit(0);

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
        
        return array($httpCode, $data);
    }

    /**
     * This function is being called from the template, semesterCodes is passed
     */
    public function import ($semesterCodes)
    {
        if (!is_array($semesterCodes))
        {
            $semesterCodes = explode(',', $semesterCodes);
        }

        $app = $this->application;
        
        $st = microtime(true);
        $dataSource = $this->getDataSource();
        $tx = $dataSource->createTransaction();
        
        // Import course archive data.
   
        $et = microtime(true);

        foreach ($semesterCodes as $active_sem) {
            $this->activeSemester = $active_sem;
            $this->import1();
            $this->importEverything();
        }

        // Everything below this line was commented out before ******************

        // $str = sprintf('Import archives in %.2f ms', $et - $st);
        // foreach ($counts as $k => $v) { $str .= "; $k $v"; }
        // $app->log('info', $str);
        
        // // Add a foreach statement of the semesterCodes
        // // $this->importUsers();
        // // $this->importEnrollments();
        // // $this->importCourses();
        

        // $st = $et; $et = microtime(true);
        // $str = sprintf('Import sections in %.2f ms', $et - $st);

        // // And commit the transaction.
        // $tx->commit();
    }

    public function import1 ()
    {
        set_time_limit(0);
        $result = false;
        
        $schemaManager = $this->application->schemaManager;
        
        $courses = $schemaManager->getSchema('Syllabus_ClassData_Course');
        $users = $schemaManager->getSchema('Syllabus_ClassData_User');
        $enrollments = $schemaManager->getSchema('Syllabus_ClassData_Enrollment');
        
        $dataSource = $courses->getDefaultDataSource();
        $tx = $dataSource->createTransaction();
        $now = new DateTime;
        
        $logs = $schemaManager->getSchema('Syllabus_ClassData_SyncLog');
        $logs->setDefaultDataSourceAlias(self::DEFAULT_DATASOURCE_ALIAS);
        $lastLog = $logs->findOne($logs->status->equals(200), array('orderBy' => array('-dt', '-id')));
        $newLog = $logs->createInstance();
        
        if ($lastLog === null)
        {
            // TODO: We need a real strategy for this. Right now, just pick a date long ago.
            $since = '1970-01-01';
        }
        else
        {
            $since = $lastLog->dt->format('c');
        }     
        
        list($status, $data) = $this->getChanges($this->activeSemester, $since);

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
            $existingCourseSet = $courses->findValues(array('externalCourseKey' => 'externalCourseKey'));
            $existingUserSet = $users->findValues(array('sfsuId' => 'sfsuId'));
            
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
                                
                                $this->deleteCourseEnrollments($tx, $enrollments, $courseId);
                            }
                            
                            if ($action['t'] == '+' || $action['t'] == '!')
                            {
                                $this->updateCourse($tx, $now, $courses, $courseId, $action['d']);
                            }
                            elseif ($action['t'] == '-')
                            {
                                $this->dropCourse($tx, $now, $courses, $courseId);
                                $existingCourseSet[$courseId] = true; // Mark as deleted.
                            }
                        }
                        elseif ($action['t'] == '+' || $action['t'] == '!')
                        {
                            $this->addCourse($tx, $now, $courses, $courseId, $action['d']);
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
                                        $this->addEnrollment($tx, $now, $enrollments, $userId, $courseId, $role);
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

   public function importEverything ()
    {
        set_time_limit(0);
        
        $schemaManager = $this->application->schemaManager;
        $courses = $schemaManager->getSchema('Syllabus_ClassData_Course');
        $users = $schemaManager->getSchema('Syllabus_ClassData_User');
        $enrollments = $schemaManager->getSchema('Syllabus_ClassData_Enrollment');

        // Get all the course data already in the database
        $dataSource = $courses->getDefaultDataSource();
        $tx = $dataSource->createTransaction();
        $now = new DateTime;
        
        // Create a list of all the userid in the dB
        $selectQuery = $dataSource->createSelectQuery($users->getEntityType());
        $selectQuery->project('id');
        $rs = $selectQuery->execute();
        $userSet = array();
        
        while ($rs->next())
        {
            $userSet[$rs->getValue('id', 'string')] = true;
        }
        
        //Create a list of all the courseid in the dB
        $selectQuery = $dataSource->createSelectQuery($courses->getEntityType());
        $selectQuery->project('id');
        $rs = $selectQuery->execute();
        $courseSet = array();
        
        while ($rs->next())
        {
            $courseSet[$rs->getValue('id', 'string')] = true;
        }

        // delete all the enrollments from dB where year_semester is the activesemster (ie. 2147)
        $deleteQuery = $dataSource->createDeleteQuery($courses->enrollments->getVia());
        $deleteQuery->setCondition($dataSource->createCondition(
            $dataSource->createSymbol('year_semester'),
            Bss_DataSource_Condition::OP_EQUALS,
            $dataSource->createTypedValue($this->activeSemester, 'string')
        )); 
    
        $deleteQuery->execute();
        
        
        // Get all the enrollments from Class Data json format and put in list
        list($status, $courseEnrollmentMap) = $this->getEnrollments($this->activeSemester);

        $unknownUserSet = array();
        $unknownCourseSet = array();
        //enrollments 
        foreach ($courseEnrollmentMap as $courseId => $enrollmentList)
        {

            if (!isset($courseSet[$courseId])) // add new courses to unknownCourse set
            {
                $unknownCourseSet[$courseId] = true;
                continue;
            }
            
            foreach ($enrollmentList as $enrollment)
            {
                $role = (substr($enrollment, 0, 1) === 'i' ? 'instructor' : 'student');
                $userId = substr($enrollment, 1);
                
                if (isset($userSet[$userId])) //id userid and class id exists then add that enrollment for the user
                {
                    $this->addEnrollment($tx, $now, $enrollments, $userId, $courseId, $role, $this->activeSemester);
                }
                else
                {
                    $unknownUserSet[$userId] = true;
                }
            }
        }

        // Add the new courses(not already in dB) from Class Data to the dB
        foreach ($this->getCourses(array_keys($unknownCourseSet)) as $courseId => $course)
        {
            
            $this->addCourse($tx, $now, $courses, $courseId, $course);
        }
        
        // Add the new user if not already in dB
        foreach ($this->getUsers(array_keys($unknownUserSet)) as $userId => $user)
        {
            $this->addUser($tx, $now, $users, $userId, $user);
        }


        
        $tx->commit();
        return $now;
    }

    protected function batches ($data, $entries)
    {
        $count = count($data);
        $batches = array();
        
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
            array(
                'deleted' => true,
                'modifiedDate' => $now,
            ),
            $users->id->equals($userId)
        );

    }

    // add the create and modify fields
    public function addUser ($tx, $now, $users, $userId, $data)
    {
        $users->insert(
            array(
                'id' => $userId,
                'firstName' => $data['first'],
                'lastName' => $data['last'],
                'emailAddress' => $data['mail'],
                'createdDate' => $now,
                'modifiedDate' => $now,
                'deleted' => false,
                
            ),
            array('transaction' => $tx)
        );
    } 

    protected function updateUser ($tx, $now, $users, $userId, $data)
    {
        $users->update(
            array(
                'firstName' => $data['first'],
                'lastName' => $data['last'],
                'emailAddress' => $data['mail'],
                'modifiedDate' => $now,
            ),
            $users->id->equals($userId),
            array('transaction' => $tx)
        );
    }

    public function addEnrollment ($tx, $now, $enrollments, $userId, $courseId, $role, $ysem)
    {

        $enrollments->insert(
            array(
                'course_id' => $courseId,
                'user_id' => $userId,
                'role' => $role,
                'year_semester' => $ysem,
                'createdDate' => $now,
                'modifiedDate' => $now,
                'deleted' => false,
                
            ),
            array('transaction' => $tx)
        );
        
    } 

    protected function dropEnrollment ($tx, $now, $courses, $userId, $courseId, $role)
    {
        $ref = $courses->enrollments;
        $dataSource = $courses->getDefaultDataSource();
        
        $deleteQuery = $tx->createDeleteQuery($ref->getVia());
        $deleteQuery->setCondition($dataSource->andConditions(array(
            $dataSource->createCondition(
                $dataSource->createSymbol('course_id'),
                Bss_DataSource_Condition::OP_EQUALS,
                $dataSource->createTypedValue($courseId, 'string')
            ),
            $dataSource->createCondition(
                $dataSource->createSymbol('user_id'),
                Bss_DataSource_Condition::OP_EQUALS,
                $dataSource->createTypedValue($userId, 'string')
            ),
        )));
        $deleteQuery->execute();
    }

    /**
     * Flags a course as having been dropped. We actually keep it around in the
     * cache because we might have resources associated with it in DIVA and we
     * want to keep that stuff.
     */
    protected function dropCourse ($tx, $now, $courses, $courseId)
    {
        $courses->update(
            array(
                'deleted' => true,
                'modifiedDate' => $now,
            ),
            $courses->id->equals($courseId)
        );
    }
    /**
     * Add a course to the cache. add prereq
     */
    public function addCourse ($tx, $now, $courses, $courseId, $data)
    {
        $num='-1';
        $sem='-1';
        $year='-1';
        $sec='-1';
        if (empty($data['sn']) || empty($data['title']))
        {
            $missing = array();
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
        
//print_r((isset($data['desc']) ? $data['desc'] : ''));
//die;

        $courses->insert(
            array(
                'id' => $courseId,
                'title' => $data['title'],
                'class_number'=> $num,
                'year' => $year,
                'semester' => $sem,
                'section_number' => $sec,
                'description' => (isset($data['desc']) ? $data['desc'] : ''),
                'createdDate' => $now,
                'modifiedDate' => $now,
                'deleted' => false,
            ),
            array('transaction' => $tx)
        );
    } 
     protected function updateCourse ($tx, $now, $courses, $courseId, $data)
    {
        if (empty($data['sn']) || empty($data['title']))
        {
            $missing = array();
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
            array(
                'id' => $courseId,
                'title' => $data['title'],
                'class_number'=> $num,
                'year' => $year,
                'semester' => $sem,
                'section_number' => $sec,
                'description' => (isset($data['desc']) ? $data['desc'] : ''),
                'createdDate' => $now,
                'modifiedDate' => $now,
                'deleted' => false,
            ),
            $courses->id->equals($courseId),
            array('transaction' => $tx)
        );
    } 

     protected function loadExistingEnrollments ($dataSource, $enrollments, $existingCourseSet, $existingUserSet)
    {
        $enrollCourseSet = array();
        $enrollUserSet = array();
        
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
            return array();
        }
        
        $existingEnrollmentSet = array();
        
        $query = $dataSource->createSelectQuery('syllabus_classdata_enrollments');
        $query->project('course_id');
        $query->project('user_id');
        $query->setCondition($dataSource->andConditions(array(
            $dataSource->createCondition(
                $dataSource->createSymbol('course_id'),
                Bss_DataSource_Condition::OP_IN,
                $dataSource->createTypedValue(array_keys($enrollCourseSet), 'string')
            ),
            $dataSource->createCondition(
                $dataSource->createSymbol('user_id'),
                Bss_DataSource_Condition::OP_IN,
                $dataSource->createTypedValue(array_keys($enrollUserSet), 'string')
            ),
        )));
        $query->orderBy('course_id', SORT_ASC);
        $query->orderBy('user_id', SORT_ASC);
        $rs = $query->execute();
        
        while ($rs->next())
        {
            $courseId = $rs->getValue('course_id', 'string');
            $userId = $rs->getValue('user_id', 'string');
            
            if (!isset($existingEnrollmentSet[$courseId]))
            {
                $existingEnrollmentSet[$courseId] = array();
            }
            
            $existingEnrollmentSet[$courseId][$userId] = true;
        } 
        return $existingEnrollmentSet;
    }

}
 


