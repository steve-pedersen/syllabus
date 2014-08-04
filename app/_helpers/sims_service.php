<?php



class sims_config
{
    private $config;

    public function __construct () {
        $this->config = get_config('enrol_sims');
    }

    public function get () {
        $result = $this->config;
        $args = func_get_args();

        if (!empty($args)) {
            $result = null;
            $field = implode('_', $args);
            if (!empty($this->config->$field)) {
                $result = $this->config->$field;
            }
        }

        return $result;
    }

    public function set () {
        $args = func_get_args();

        if (!empty($args) && count($args) > 1) {
            $value = array_pop($args);
            $field = implode('_', $args);
            if ($field) {
                set_config($field, $value, 'enrol_sims');
            }
        }
    }
}

/**
 * Loads SIS information from the SIMS web service.
 * 
 * @author      Daniel A. Koepke (dkoepke@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class sims_service
{
    private  $engine;
    private  $endpoint;
    private  $api_key;
    private  $api_secret;
    
    
    
    public function __construct ()
    {
        

        //$this->engine = $engine;
        $this->endpoint = 'https://classdata.sfsu.edu/';
        $this->api_key = 'ca1a3f6f-7cac-4e52-9a0a-5cbf82b16bc9';
        $this->api_secret = '4af2614e-142d-4db8-8512-b3ba13dd0143';
    }
    

    public function import ($diff = true)
    {
        set_time_limit(0);
        $result = false;
        $now = null;

        foreach ($this->engine->get_active_categories() as $id => $category) {
            if ($diff) {
                $this->engine->log('SIMS Enrollment diff on ' . $category->name);
                if ($now = $this->importCategory($id, $category->lastlogtime)) {
                    $this->setLastLogTime($now, $id);
                }
            } else {
                $this->engine->log('SIMS Enrollment full on ' . $category->name);
                if (!$now) {
                    $now = time();
                    $this->setLastLogTime($now);
                }

                $this->normalizeEnrollments($id);
            }
        }
    }

    /**
     * This method will get all of the enrollments in sims and make sure
     * that only those and only those users are enrolled in the courses.
     * 
     * @param  integer $categoryid The id of the category to check.
     */
    public function normalizeEnrollments ($categoryid)
    {
        $now = time();
        $nowDisplay = date('c', $now);
        list($status, $data) = $this->getEnrollments($categoryid);
        
        $existing = $this->loadCourseEnrollments(array_keys($data));
        $existingUserSet = $this->getExistingUserIds();
        $existingCourseSet = $this->getExistingCourseIds();

        foreach ($data as $courseId => $enrollments) {
            if (isset($existingCourseSet[$courseId])) {
                foreach ($enrollments as $userId) {
                    $role = substr($userId, 0, 1);
                    $userId = substr($userId, 1);
                    if (array_key_exists($userId, $existingUserSet)) {
                        if (!isset($existing[$courseId][$userId])) {
                            if ($this->engine->user_has_access($userId) && $this->engine->course_has_access($courseId)) {
                                $role = ($role == 's' ? 'student' : 'instructor');
                                $this->addEnrollment($now, $existingUserSet[$userId], $courseId, $role);
                                $this->engine->log("SIMS Enrollment Normalization: add user $userId to course $courseId as $role: $nowDisplay");
                            }
                        }
                    }
                    
                    unset($existing[$courseId][$userId]);
                }
            }
        }

        foreach ($existing as $courseId => $enrollments) {
            if (isset($existingCourseSet[$courseId])) {
                foreach ($enrollments as $userId => $roles) {
                    if (isset($existingUserSet[$userId])) {
                        foreach ($roles as $role) {
                            $this->dropEnrollment($now, $existingUserSet[$userId], $courseId, $role);
                            $this->engine->log("SIMS Enrollment Normalization: drop user $userId from course $courseId as $role: $nowDisplay");
                        }
                    }
                }
            }
        }
    }


    public function importCategory ($categoryid, $since = '1970-01-01') 
    {
        $now = time();
        list($status, $data) = $this->getChanges($categoryid, $since);
        $nowDisplay = date('c', $now);
        if ($status != 200)
        {
            $newLog = new stdClass;

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

            $this->engine->log('SIMS Enrollment ErrorCode: ' . $newLog->errorCode);
            $this->engine->log('SIMS Enrollment ErrorMessage: ' . $newLog->errorMessage);
        }
        else
        {
            // Keeps track of existing courses and users as we process the batches.
            $existingUserSet = $this->getExistingUserIds();
            $existingCourseSet = $this->getExistingCourseIds();

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
                                
                                $this->deleteCourseEnrollments($courseId);
                            }
                            
                            if ($action['t'] == '+' || $action['t'] == '!')
                            {
                                $this->updateCourse($now, $courseId, $action['d']);
                            }
                            elseif ($action['t'] == '-')
                            {
                                $this->dropCourse($now, $courseId);
                                $existingCourseSet[$courseId] = true; // Mark as deleted.
                                $this->engine->log('Course dropped: ' . $courseId . ' time: ' . $nowDisplay);
                            }
                        }
                        elseif ($action['t'] == '+' || $action['t'] == '!')
                        {
                            if ($id = $this->addCourse($now, $courseId, $action['d'])) {
                                $existingCourseSet[$courseId] = false;
                                $this->engine->log('New course added. SIMS ID: ' . $courseId . ' id: ' . $id . ' time: ' . $nowDisplay);
                            }
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
                                    $this->updateUser($now, $userId,  $action['d']);
                                    break;
                                case '-':
                                    $this->dropUser($now, $userId);
                                    unset($existingUserSet[(string)$userId]);
                                    $this->engine->log('User dropped: ' . $userId . ' time: ' . $nowDisplay);
                                    break;
                            }
                        }
                        elseif ($action['t'] == '+' || $action['t'] == '!')
                        {
                            if ($id = $this->addUser($now, $userId, $action['d'])) {
                                $existingUserSet[(string)$userId] = $id;
                                $this->engine->log('New user added. username: ' . $userId . ' id: ' . $id . ' time: ' . $nowDisplay);
                            }
                        }
                    }
                }
            }
            
            $existingEnrollmentSet = $this->loadExistingEnrollments($data['enrollments'], $existingCourseSet, $existingUserSet);
            
            // Enrollments.
            foreach ($data['enrollments'] as $courseId => $courseEnrollList)
            {
                if (array_key_exists($courseId, $existingCourseSet))
                {
                    foreach ($courseEnrollList as $action)
                    {
                        $role = ($action[1] == 's' ? 'student' : 'instructor');
                        $userId = substr($action, 2);

                        if (array_key_exists($userId, $existingUserSet))
                        {
                            switch ($action[0])
                            {
                                case '+':
                                    if (!isset($existingEnrollmentSet[$courseId]) || !isset($existingEnrollmentSet[$courseId][$userId]))
                                    {
                                        $this->addEnrollment($now, $existingUserSet[$userId], $courseId, $role);
                                    }
                                    break;
                                case '-':
                                    $this->dropEnrollment($now, $existingUserSet[$userId], $courseId, $role);
                                    break;
                            }
                        }
                        elseif ($this->engine->user_has_access($userId))
                        {
                            $this->engine->log("Enrollment {$action} in {$courseId} for non-existent user: {$userId}" . ' time: ' . $nowDisplay);
                        }
                    }
                }
                elseif ($this->engine->course_has_access($courseId))
                {
                    $this->engine->log("Enrollment for non-existent course: {$courseId}" . ' time: ' . $nowDisplay);
                }
            }
        }
        
        return $now;
    }


    protected function getExistingUserIds () {
        return $this->engine->get_existing_userids();
    }


    protected function getExistingCourseIds () 
    {
        return $this->engine->get_existing_courseids();
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
    
    
    protected function deleteCourseEnrollments ($courseId)
    {
        $this->engine->delete_course_enrollments($courseId);
    }


    protected function setLastLogTime ($logtime, $categoryid = null) {
        $this->engine->set_lastlogtime($logtime, $categoryid);
    }
    
    
    /**
     * Flags a course as having been dropped. We actually keep it around in the
     * cache because we might have resources associated with it in DIVA and we
     * want to keep that stuff.
     */
    protected function dropCourse ($now, $courseId)
    {
        $this->engine->drop_course($now, $courseId);
    }
    
    
    /**
     * Add a course to the cache.
     */
    protected function addCourse ($now, $courseId, $data)
    {
        $course = array(
            'idnumber' => $courseId,
            'shortname' => $data['sn'],
            'fullname' => $data['title'],
        );

        return $this->engine->add_course($now, $courseId, $course);
    }
    
    
    /**
     */
    protected function updateCourse ($now, $courseId, $data)
    {
        $course = array(
            'idnumber' => $courseId,
            'shortname' => $data['sn'],
            'fullname' => $data['title'],
        );

        $this->engine->update_course($now, $courseId, $course);
    }
    
    
    /**
     */
    protected function dropUser ($now, $userId)
    {
        $this->engine->drop_user($now, $userId);
    }
    
    
    /**
     */
    protected function addUser ($now, $userId, $data)
    {
        $user = array(
            'username' => $userId,
            'idnumber' => $userId,
            'firstname' => $data['first'],
            'lastname' => $data['last'],
            'email' => $data['mail'],
        );

        return $this->engine->add_user($now, $userId, $user);
    }
    
    
    /**
     */
    protected function updateUser ($now, $userId, $data)
    {
        $user = array(
            'username' => $userId,
            'idnumber' => $userId,
            'firstname' => $data['first'],
            'lastname' => $data['last'],
            'email' => $data['mail'],
        );

        return $this->engine->update_user($now, $userId, $user);
    }
    
    
    /**
     */
    protected function addEnrollment ($now, $userId, $courseId, $role)
    {
        $this->engine->add_enrollment($now, $userId, $courseId, $role);
    }
    
    
    /**
     */
    protected function dropEnrollment ($now, $userId, $courseId, $role)
    {
        $this->engine->drop_enrollment($now, $userId, $courseId, $role);
    }
    
    
    /**
     */
    protected function loadExistingEnrollments ($enrollments, $existingCourseSet, $existingUserSet)
    {
        return $this->engine->load_existing_enrollments($enrollments, $existingCourseSet, $existingUserSet);
    }

    /**
     */
    protected function loadCourseEnrollments ($courses)
    {
        return $this->engine->load_course_enrollments($courses);
    }
    
    
    protected function signResource ($resource, $paramMap)
    {
        $url = rtrim($this->endpoint, '/') . '/' . $resource;
        $paramMap['a'] = $this->api_key;
        uksort($paramMap, 'strcmp');
        
        $params = array();
        foreach ($paramMap as $k => $v) { $params[] = urlencode($k) . '=' . urlencode($v); }
        $url .= '?' . implode('&', $params);
        
        return $url . '&s=' . sha1($this->api_secret . $url);
    }
    
    protected function getEnrollments ($semester, $role = null)
    {
        $paramMap = array('ids' => true);
        
        if ($role)
        {
            $paramMap['role'] = $role;
        }
        
        $url = $this->signResource("enrollments/{$semester}", $paramMap);
        $req = new HttpRequest($url, HTTP_METH_GET);
        $req->send();
        
        $body = $req->getResponseBody();
        $data = null;
        
        if (!empty($body))
        {
            $data = @json_decode($body, true);
        }
        
        return array($req->getResponseCode(), $data);
    }
    
    public function getChanges ($semester)
    {
        
       
        $since="2014-06-01";
        $url = $this->signResource("changes/{$semester}", array('since' => $since));
     // print($url);die;
        $req = new HttpRequest($url, HTTP_METH_GET);
        $req->send();
        
        $body = $req->getResponseBody();
        @set_time_limit(0);
        $data = null;
        /*echo "<pre>";
        print_r($body);
        die;*/
        
        if (!empty($body))
        {
            $data = json_decode($body,true);
        }
        
        return array($req->getResponseCode(), $data);
    }
    
    public function getCourses($idList)
    {
        $url = $this->signResource('courses', array('include' => 'description,prerequisites'));
        $req = new HttpRequest($url, HTTP_METH_POST);

        $req->setPostFields(array('ids' => implode(',', $idList)));
        $req->send();
        
        $body = $req->getResponseBody();
        $data = null;
        
        if (!empty($body) && $req->getResponseCode() === 200)
        {
            $data = @json_decode($body, true);
            return $data['courses'];
        }
        
        return false;
    }
    
    public function getUsers ($idList=null)
    {
        $url = $this->signResource('users', array('include' => 'description,prerequisites'));
         
        
        $req = new HttpRequest($url, HTTP_METH_POST);
        $req->setPostFields(array('ids' => implode(',', $idList)));
        $req->send();
        
        $body = $req->getResponseBody();
       
        $data = null;
        
        if (!empty($body) && $req->getResponseCode() === 200)
        {
            $data = @json_decode($body, true);
            return $data['users'];
        }
        
        return false;
        
    }

}
