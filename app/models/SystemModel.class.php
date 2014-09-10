<?php

/**
 * Require classes if necessary (autoload function will not be available if running from Command Line so they must be manually included)
 */
require_once('_helpers/Messages.class.php');
require_once('_helpers/sims_service.php');


/**
 * SystemModel
 */
class SystemModel extends BaseModel {	
	private $sims_service;
    private $r; //this is the result produced by the sims_service
    private $enrol;
    private $semesters=array();
    private $semester_Names=array();
    private $semester_Years=array();

    static $OldSemesterCodes = array(
        1 => 'Winter',
        2 => 'Spring',
        3 => 'Summer',
        4 => 'Fall',
    );

    static $NewSemesterCodes = array(
        1 => 'Winter',
        3 => 'Spring',
        5 => 'Summer',
        7 => 'Fall',
    );
	    
	/**
     * Run an update of the system from the SIMS database
     * Used by the cron to update the active semester ids only
     */
	public function systemUpdate() {
		
        // set a flag for whether the script was executed via command line
        // for this, we need to check $_SERVER['argv'][1] since [0] is the name of the script
        $is_command_line_script = (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'cli')
            ? true
            : false;
        
		$run = false;
		
		// if the request was not initiated via command line
		if($is_command_line_script) {
			$run = true;
		} else {
			if((is_object($this->Permissions) && $this->Permissions->isAdmin())) {
				$run = true;				
			} else {
				Messages::addMessage('You do not have permission to run system updates', 'error');
			}
		}
		
		if($run) {
                
                
                //Get all the active semester ids from the the DB
                $this->query = "SELECT id FROM semester_info WHERE activity= 1 ORDER BY id;";
                $semArray = $this->executeQuery();
            
                if($semArray['count']>0){
                    //for each active semester id from the dB,
                    // run the Sims service::getChanges to get the Active semester data
                    foreach ($semArray['data'] as $active_sem) {
                        //Update only semesters that are of the new format, ie. 2147 or after
                        $length = strlen($active_sem['id']);
                        if($length==4){
                            $this->sims_service = new sims_service();
                            $this->r= $this->sims_service->getChanges($active_sem['id']);
                            $this->enrol= $this->sims_service->getEnrollments($active_sem['id']);
                            //Load data from SIMS to the syllabi dB
                            $this->importUsers();
                            $this->importEnrollment($active_sem['id']);
                            $this->importSyllabi();
                        }
                    }
		        }
            Messages::addMessage('The System was successfully updated', 'success');
           // $this->redirect = 'https://www.google.com/';
            return true;
		}
	}

    /**
     * Get all semester information(id,visibility,activity) from the dB
     * @param string $sem, which is the semester
     * @return array Return the semester to display in table
     */
    public function getData() {
            $this->query = "SELECT id,visibility,activity FROM semester_info ORDER BY id;";
            $semesters = $this->executeQuery();
            
            if($semesters['count']>0){
                //separate the semesters string by ','
                foreach($semesters['data'] as $semester){
                    //store each semester in the semesters array
                    $this->setSemestersArray($semester['id'],$semester['visibility'],$semester['activity']);   
                }   
            }

            krsort($this->semesters);
    }

    /**
     * Get all current semesters in dB to populate the textfield with attainable ids
     * @return array Return the semester ids to display in textfield as default values if the field is empty
     */
    public function populateTextField() {
        $temp = array();
        $default = $this->semesters_string;
        if( $default == "") //if empty string, then replace with current ids in db to help user
        {
            $this->query = "SELECT id FROM semester_info ORDER BY id;";
            $semArray = $this->executeQuery();
            if($semArray['count']>0){
                //separate the semesters string by ','
                foreach($semArray['data'] as $semester){
                    //store each semester in the temp array
                    array_push($temp, $semester['id']);     
                }
                $default = implode(',', $temp);
            }
        }
        return $default;            
    }

    /**
     * Saves the visibility and activity settings(boolean values) for each semester id in the dB
     */
    public function saveChanges() {

          //set visibility to true in dB for each checked visibility checkbox
          if(isset($this->vid) && is_array($this->vid) && count($this->vid)>0) {

                foreach($this->vid as $k => $v) {
                    $this->query = "UPDATE semester_info SET visibility=1 WHERE id='$v';";
                    $this->executeQuery();
                }
                //set visibility to false for any unchecked visibility checkbox
                $this->query = "UPDATE semester_info SET visibility=0 WHERE id NOT IN ( '" . implode($this->vid, "', '") . "' )";
                $this->executeQuery();
            }
            //set activity to true in dB for each checked activity checkbox
          if(isset($this->aid) && is_array($this->aid) && count($this->aid)>0) {
                foreach($this->aid as $k => $a) {
                    $this->query = "UPDATE semester_info SET activity=1 WHERE id='$a';";
                    $this->executeQuery();
                }
                //set activity to false for any unchecked activity checkbox
                $this->query = "UPDATE semester_info SET activity=0 WHERE id NOT IN ( '" . implode($this->aid, "', '") . "' )";
                $this->executeQuery();
            }
            $this->redirect = 'system/update'; //refresh the page
            Messages::addMessage('Settings for the selected semesters have been saved', 'success');
            return true;
            
    }

    /**
     * Form button: Sets and Validates the semester ids in the dB:semester_info
     */
    public function setSemesters() {
        //initialize semesters string to be textfield string of ids
       $semesters = $this->semesters_string;
       //if string is empty, then delete all current ids in dB
       if(empty($semesters)){
            $this->query ="DELETE FROM semester_info;";
            $this->executeQuery();
            return false;
       }

       $id= explode(',', $semesters);
       //validate each semester id in the string
       foreach($id as $semester){
                //use regex pattern to check for incorrect id formats
                $isMatch=$this->validateSemester($semester);
                if(!$isMatch){
                    $error='Semester '.$semester.' is NOT in correct format';
                    Messages::addMessage($error, 'error');
                    return false;
                }
        }
        
        //flip the key with the values so the key is now the unique student id
        $web_results=array_flip($id);
        //delete the records that are not in the string of ids
        $this->query = "DELETE FROM semester_info WHERE id NOT IN ( '" . implode($id, "', '") . "' )";
        $dB_results = $this->executeQuery();
    
        
        //get list of all ids in dB
        $this->query = "SELECT id FROM semester_info;";
        $dB_results = $this->executeQuery();
        //if dB has records, then compare string with each dB records and unset duplicates
         if ($dB_results['count']>0) {
             //parse the dbResults to get the id
            foreach ($dB_results['data'] as $sId) {
            
                if(array_key_exists($sId['id'], $web_results)){
                    unset($web_results[$sId['id']]);     
                }
            }
         }
        
        //create an array of the leftover ids, which are the leftover keys from web_results
        $newIds = array_keys($web_results);
        //insert the id,visibility, activity for each newId into the database
        foreach ($newIds as $nid) {
            $this->query = "INSERT INTO semester_info (id, visibility, activity)  
            Values('$nid',0,0);";
        $this->executeQuery(); 
        }
        
    }

    /**
     * Get all semesters for the current user
     * @param string $sem, which is the semester
     * @return array Return the semester to display in table
     */
    public function getSemesters() {
        return $this->semesters;
    }
    
   
    /**
     * Set each semester information for the current user in the semesters array
     * @param string semester id, boolean visibility, boolean activity 
     */
    private function setSemestersArray($sem,$vis,$act) {
        $length = strlen("$sem");
        $semString = "$sem";
        switch ($length) {
            case 5:
                $semester = substr($semString, 4, 1);
                $name = self::$OldSemesterCodes[$semester];
                $year = substr($semString, 0, 4);
                $sortKey = $semString;
                break;
            case 4:
                $semester = substr($semString, 3, 1);
                $name = self::$NewSemesterCodes[$semester];
                $year = '20' . substr($semString, 1, 2);
                $sortKey = $semString[0] . '0' . substr($semString, 1);
                break;
        }
        
        if($vis==1){
            $vis = 'checked= "checked"';
        }else{
            $vis = '';
        }
        if($act==1){
            $act = 'checked= "checked"';
        }else{
            $act = '';
        }
        $temp = array('semester_id'=>$sem,
                      'semester_name' =>$name,
                      'semester_year'=> $year,
                      'semester_visibility' =>$vis,
                      'semester_activity' =>$act);

        $this->semesters[$sortKey] = $temp;
    }

    
    /**
     * Validate the semester by checking if string is in correct format("20121")
     */
    private function validateSemester($sem) {
        $regex= '/^2([0-9]{2,3})([1-9])$/';
        if(preg_match($regex, $sem, $matches)){
            //if there is a match then relate the second match(the semester number 1,3,5,7) 
            //with the semester name(Winter,Spring,Summer,Fall)
            
            return array($matches,true);
        }
        return false;
    }

    /**
     * Import users into the system from the SIMS file
     */
    private function importUsers() {
        //drop temporary table     
        $this->query= "DROP TABLE IF EXISTS susers";
        $this->executeQuery();
        // build the temporary table		
        $this->query= "CREATE TABLE `susers` (
            `SFSUid` INT( 9 )  ,
            `External_Person_Key` INT( 6 )  ,
            `User_ID` VARCHAR( 100 )  ,
            `Passwd` VARCHAR( 6 )  ,
            `Firstname` VARCHAR( 30 )  ,
            `Lastname` VARCHAR( 30 )  ,
            `Email` VARCHAR( 30 )  ,
            `Institution_Role` VARCHAR( 20 )  ,
            `System_Role` VARCHAR( 20 )  ,
            `Available_Ind` VARCHAR( 1 )  ,
            `Row_Status` VARCHAR( 10 ) 
            ) TYPE = MYISAM ;";
        $this->executeQuery();

       

       //Load JSON data of users into temp table
        foreach( $this->r[1]['users'] as $sId => $user )
        {
            foreach( $user as $d )
            {
                
                $d['d']['first'] = $this->mysqli->escape_string($d['d']['first']);
                $d['d']['last'] = $this->mysqli->escape_string($d['d']['last']);
                $d['d']['mail'] = $this->mysqli->escape_string($d['d']['mail']);
                $this->query = "INSERT INTO susers (SFSUid, Firstname, Lastname, Email)  
                Values('".$sId." ','".$d['d']['first']." ',' ".$d['d']['last']." ','".$d['d']['mail']."');";
                $this->executeQuery();
                
            }
        }
        
        //insert (update duplicates)
        $this->query = "INSERT INTO users (user_id,user_fname, user_lname, user_email)  
            SELECT SFSUid,Firstname, Lastname, Email FROM susers WHERE Firstname != ''
            ON DUPLICATE KEY UPDATE user_id=SFSUid, user_fname=Firstname, user_lname=Lastname, user_email=Email;";
        $this->executeQuery(); 
     
    }



    /**
     * Import the data into the enrollment table
     */
    private function importEnrollment($active_sem) {
        //drop temporary table     
        $this->query= "DROP TABLE IF EXISTS senroll;";
        $this->executeQuery();
        // build the temporary table
        $this->query= "CREATE TABLE `senroll` (
            `SFSUid` VARCHAR( 15 ) NOT NULL ,
            `External_Course_Key` VARCHAR( 13 ) NOT NULL ,
            `External_Person_Key` INT( 6 )  ,
            `Role` VARCHAR( 10 )  ,
            `Sem_Year` VARCHAR( 7 ) DEFAULT '2147'  ,
            `Available_Ind` VARCHAR( 1 ) ,
            `Row_Status` VARCHAR( 10 ) 
            ) TYPE = MYISAM ;";
        $this->executeQuery();

        // load into enrollment table
        //ck=class Key, role= student/instructor, v= value of each class key, sId= student Id
        foreach ( $this->enrol[1] as $cK=>$v ) 
        {
            foreach ( $v as $sId )
            {
                // Remove the +- and si characters from the beginning of student id
                $sId = $this->mysqli->escape_string($sId);
                $role=$sId[0]; // this value is i/s and we need to change to instructor/student
                $role = ($role=='i') ? 'instructor' : 'student' ;
                $sId=substr( $sId, 1 );
                $sId = trim($sId);
                $this->query = "INSERT INTO senroll (External_Course_Key, SFSUid, Role)
                Values('".$cK." ', ".$sId.",'".$role." ');";
                $this->executeQuery();
            }     
        }
    
        //insert (update duplicates)
        $this->query = "INSERT INTO enrollment (enroll_class_id, enroll_user_id, enroll_role)  
            SELECT External_Course_Key,SFSUid,Role FROM senroll 
            ON DUPLICATE KEY UPDATE enroll_class_id=External_Course_Key,enroll_user_id=SFSUid, enroll_role=Role;";
        $this->executeQuery();

        $this->query= "Select Count(*) from senroll;";
        if($this->executeQuery()>0){
            $this->query = "Create index CK on senroll (External_Course_Key);";
            $this->executeQuery();
            $this->query = "Create index PK on senroll (External_Person_Key);";
            $this->executeQuery();
            $this->query = "Create index RL on senroll (Role);";
            $this->executeQuery();

            $this->query = "SELECT id,visibility,activity FROM semester_info ORDER BY id;";
            $count = $this->executeQuery();
            
            //if senroll is not empty, then delete the records in enrollment that are not in senroll
        $this->query=   "DELETE FROM enrollment 
                        WHERE (enroll_user_id,enroll_class_id,enroll_role) IN
                        (SELECT enroll_user_id,enroll_class_id,enroll_role
                        FROM (
                        SELECT enroll_user_id,enroll_class_id,enroll_role
                        FROM enrollment 
                        INNER JOIN syllabus 
                        ON syllabus.syllabus_id = enrollment.enroll_class_id  
                        LEFT JOIN senroll 
                        ON senroll.SFSUid = enrollment.enroll_user_id 
                        AND senroll.External_Course_Key = enrollment.enroll_class_id 
                        AND senroll.Role = enrollment.enroll_role 
                        WHERE senroll.External_Person_Key IS NULL
                        AND senroll.External_Course_Key IS NULL
                        AND senroll.Role IS NULL
                        AND syllabus.syllabus_sem_id = '".$active_sem."'
                        )x);";
        $this->executeQuery();             
        }

    }

    
    /**
     * Import data into the syllabi table
     */
    private function importSyllabi() {
        //local variables
        $c_number;
        $c_section;
        $c_sem=0;
        $c_year;
        $c_sem_id;
        $this->query="DROP TABLE IF EXISTS sclass_desc;";
        $this->executeQuery();
        $this->query="DROP TABLE IF EXISTS classes;";
        $this->executeQuery();
        // build the temporary table		
        $this->query= "CREATE TABLE `sclass_desc` (
            `External_Course_Key` VARCHAR( 13 ) ,
            `title` VARCHAR( 100 )  ,
            `sem_id` VARCHAR( 10 )  ,
            `units` TINYINT( 1 )  ,
            `catlg` INT( 5 )  ,
            `school` INT( 3 )  ,
            `dept` INT( 3 )  ,
            `computer_req` TINYINT( 1 )  ,
            `crs_description` TEXT  ,
            `prereq_description` TEXT 
            ) TYPE = MYISAM ;";
        $this->executeQuery();

        /*Also, we need to get the rest of the necessary information for the syllabus table*/
        
        // build the temporary table        
        $this->query= "CREATE TABLE `classes` (
            `External_Course_Key` VARCHAR( 13 ) NOT NULL ,
            `Course_ID` VARCHAR( 30 ) NOT NULL ,
            `Course_Name` VARCHAR( 100 ) NOT NULL ,
            `Course_Num` VARCHAR( 100 ) NOT NULL ,
            `Course_Sec` INT(2) NOT NULL ,
            `Course_Sem` INT(2) NOT NULL ,
            `Course_Year` INT(5) NOT NULL ,
            `Available_Ind` VARCHAR( 1 ) NOT NULL ,
            `Row_Status` VARCHAR( 10 ) NOT NULL ,
            `Abs_Limit` INT( 6 ) NOT NULL ,
            `Upload_Limit` INT( 6 ) NOT NULL,
            PRIMARY KEY (`External_Course_Key`)
            ) ENGINE = MYISAM ;";
        $this->executeQuery();
        
       
        // load into temporary table
        foreach ( $this->r[1]['courses'] as $cK => $v ) 
        {

            foreach ( $v as $s ) //$v= each value of the course key
            {
                //only insert into syllabus dB if there is a key called ['d'] in the JSON object
                //which contains info for the title and course description
                if (array_key_exists("d",$s)){

                
                    if (array_key_exists("title",$s['d']))
                    {
                        $s['d']['title'] = $this->mysqli->escape_string($s['d']['title']);
                    
                    }else{
                        $s['d']['title'] = NULL;
                    }
                    if (array_key_exists("desc",$s['d']))
                    {
                        $s['d']['desc'] = $this->mysqli->escape_string($s['d']['desc']);
                    }else{
                        $s['d']['desc'] = NULL;
                    }
                    //Parse the Course Section
                    $c_sec = preg_split('[-]', $s['d']['sn']);
                    $c_sec = $c_sec[2];

                    //Parse the semester from the short name
                    if (strpos($s['d']['sn'],'Fall') !== false) {
                        $c_sem=7;
                    }elseif (strpos($s['d']['sn'],'Winter') !== false) {
                        $c_sem=1;
                    }elseif (strpos($s['d']['sn'],'Spring') !== false) {
                        $c_sem=3;
                    }else{
                        $c_sem=5;
                    }
                    //Parse the Course year and build the semester id string '2147'
                    $c_year = preg_split('[-]', $s['d']['sn']);
                    $c_year = $c_year[4];
                    $c_sem_id = preg_split('[0]', $c_year);
                    $c_sem_id = '2'.$c_sem_id[1].$c_sem;
                
                    $this->query = "INSERT INTO sclass_desc (External_Course_Key, title, crs_description,sem_id)
                    Values('".$cK." ', '".$s['d']['title']."','".$s['d']['desc']."','".$c_sem_id." ');";
                    $this->executeQuery(); 

                    //Enter 2nd set of data into 'classes' after parsing the course key($cK)
                    $this->query = "INSERT INTO classes (External_Course_Key, Course_Name, Course_Sem,Course_Sec, Course_Year)
                    Values('".$cK." ', '".$s['d']['sn']."','".$c_sem." ','".$c_sec." ','".$c_year." ');";
                    $this->executeQuery(); 

                } 
            }    
        }  
        
         //A_U-0425-01-Fall-2014
        // insert first set of data into syllabus(update duplicates)
        $this->query= "INSERT INTO syllabus (syllabus_id, syllabus_class_title, syllabus_class_description,syllabus_sem_id)  
            SELECT External_Course_Key,title, crs_description,sem_id FROM sclass_desc
            ON DUPLICATE KEY UPDATE syllabus_class_title=title";
        $this->executeQuery();
        
        // insert second set of data into syllabus(update duplicates)
        $hash_str = 'ai#9@LC8_2*';        
       $this->query= "INSERT INTO syllabus (syllabus_id, syllabus_class_number, syllabus_class_section, syllabus_class_semester, syllabus_class_year, syllabus_view_token )  
            SELECT 
                External_Course_Key,
                REPLACE(SUBSTR(Course_Name, 1, 9),'-',' '),
                Course_Sec, 
                Course_Sem, 
                Course_Year,
                MD5(CONCAT('" . $hash_str . "', External_Course_Key))
            FROM classes
            ON DUPLICATE KEY UPDATE 
                syllabus_class_number=REPLACE(SUBSTR(Course_Name, 1, 9),'-',' '), 
                syllabus_class_section=Course_Sec, 
                syllabus_class_semester=Course_Sem,
                syllabus_class_year=Course_Year,
                syllabus_view_token=MD5(CONCAT('" . $hash_str . "', External_Course_Key))
            ";
        $this->executeQuery();
        
    }


    /**
     * Remove orphaned data from the database. Only information that exists in the snapshot should exist in the DB
     */
	private function deleteOrphans() {
        
		$this->query= "DELETE FROM syllabus WHERE syllabus.syllabus_id NOT IN (SELECT External_Course_Key FROM snapshot_classes) AND syllabus.syllabus_id NOT LIKE '%draft-%'";
		$this->executeQuery();
        
		$this->query= "DELETE FROM assignments WHERE assignments.syllabus_id!='repository' AND assignments.syllabus_id NOT IN (SELECT External_Course_Key FROM snapshot_classes) AND assignments.syllabus_id NOT LIKE '%draft-%'";
		$this->executeQuery();
        
        $this->query= "DELETE FROM materials WHERE materials.syllabus_id!='repository' AND materials.syllabus_id NOT IN (SELECT External_Course_Key FROM snapshot_classes) AND materials.syllabus_id NOT LIKE '%draft-%'";
		$this->executeQuery();
        
        $this->query= "DELETE FROM methods WHERE methods.syllabus_id!='repository' AND methods.syllabus_id NOT IN (SELECT External_Course_Key FROM snapshot_classes) AND methods.syllabus_id NOT LIKE '%draft-%'";
		$this->executeQuery();
        
        $this->query= "DELETE FROM objectives WHERE objectives.syllabus_id!='repository' AND objectives.syllabus_id NOT IN (SELECT External_Course_Key FROM snapshot_classes) AND objectives.syllabus_id NOT LIKE '%draft-%'";
		$this->executeQuery();
        
        $this->query= "DELETE FROM policies WHERE policies.syllabus_id!='repository' AND policies.syllabus_id NOT IN (SELECT External_Course_Key FROM snapshot_classes) AND policies.syllabus_id NOT LIKE '%draft-%'";
		$this->executeQuery();
        
        $this->query= "DELETE FROM schedules WHERE schedules.syllabus_id!='repository' AND schedules.syllabus_id NOT IN (SELECT External_Course_Key FROM snapshot_classes) AND schedules.syllabus_id NOT LIKE '%draft-%'";
		$this->executeQuery();
        
        $this->query= "DELETE FROM teaching_assistants WHERE teaching_assistants.syllabus_id!='repository' AND teaching_assistants.syllabus_id NOT IN (SELECT External_Course_Key FROM snapshot_classes) AND teaching_assistants.syllabus_id NOT LIKE '%draft-%'";
		$this->executeQuery();
        
        $this->query= "DELETE FROM permissions WHERE permissions.syllabus_id NOT IN (SELECT External_Course_Key FROM snapshot_classes)";
		$this->executeQuery();
		
	}
	
	
	/**
	 * Run modifications to the DB
	 * Grants ownership and other permissions to a TEST user (available via test IDP) for QA and iLearn integration testing
	 */
	public function createUsers() {
		if(DEBUG_MODE) {
			// insert the user
			$this->query = "INSERT IGNORE INTO users SET user_id='T60000001', user_fname='Test 1', user_lname='User', user_preferred_name='Testing User 1' ";
			$this->executeQuery();
			$this->query = "INSERT IGNORE INTO users SET user_id='T60000002', user_fname='Test 2', user_lname='User', user_preferred_name='Testing User 2' ";
			$this->executeQuery();
			$this->query = "INSERT IGNORE INTO users SET user_id='T60000003', user_fname='Test 3', user_lname='User', user_preferred_name='Testing User 3' ";
			$this->executeQuery();
			$this->query = "INSERT IGNORE INTO users SET user_id='T60000004', user_fname='Test 4', user_lname='User', user_preferred_name='Testing User 4' ";
			$this->executeQuery();
			// remove permissions just in case .. then re-insert
			$this->query = "DELETE FROM permissions WHERE user_id='T60000001';";
			$this->executeQuery();
			$this->query = "INSERT INTO permissions SET user_id='T60000001', permission='admin';";
			$this->executeQuery();
			
			Messages::addMessage('Users created.  <a href="login">Login</a> to gain access.', 'success');
			$this->redirect = 'system/create_users';
			$return = true;
		} else {				
			Messages::addMessage('Testing users can only be created for sites that are in debug mode (non-production sites).  Set debug mode in the config file.', 'error');
			$return = false;
		}
		
		return $return;
	}


	/**
	 * Assign a specific user to a specific course in the selected role
	 */
	public function assignUser() {
		if($this->Permissions->isAdmin()) {
			$valid = true;
			
			// make sure we have a valid user
			if(isset($this->enroll_user_id)) {
				$U = new UsersModel;
				$this->query = sprintf("SELECT * FROM users WHERE user_id='%s';", $this->enroll_user_id);
				$result = $this->executeQuery();
				if($result['count']) {
					$valid = true;
				} else {
					$valid = false;
					Messages::addMessage('User does not exist in the Database', 'error');
				}
			} else {
				$valid = false;
			}
			
			// make sure the course exists in the enrollment table
			if(isset($this->enroll_class_id)) {
				$this->query = sprintf("SELECT * FROM enrollment WHERE enroll_class_id='%s';", $this->enroll_class_id);
				$result = $this->executeQuery();
				if($result['count']) {
					$valid = true;
				} else {
					$valid = false;
					Messages::addMessage('Class does not exist in the Database', 'error');
				}
			} else {
				Messages::addMessage('Please enter a class id', 'error');
				$valid = false;
			}
			
			if($valid) {
				switch($this->enroll_role) {
					case 'instructor':
						$this->query = sprintf("UPDATE enrollment SET enroll_user_id='%s' WHERE enroll_class_id='%s' AND enroll_role='instructor';", $this->enroll_user_id, $this->enroll_class_id);
						$this->executeQuery();
						Messages::addMessage('User assigned as instructor', 'success');
						$return = true;
						break;
					
					case 'student':
						$this->query = sprintf("INSERT INTO enrollment SET enroll_user_id='%s', enroll_class_id='%s', enroll_role='student';", $this->enroll_user_id, $this->enroll_class_id);
						$this->executeQuery();
						Messages::addMessage('User assigned as student', 'success');
						$return = true;
						break;
					
					default:
						Messages::addMessage('Invalid Action', 'error');
						$return = false;
						break;
				}
			}
			
			$return = true;
		} else {
			Messages::addMessage('You must be an administrator to perform this action.', 'error');
			$return = false;
		}
		
		$this->redirect = 'system/assign';
		return $return;
	}


	
} // end class
