<?php

/**
 * Require classes if necessary (autoload function will not be available if running from Command Line so they must be manually included)
 */
require_once('_helpers/Messages.class.php');


/**
 * SystemModel
 */
class SystemModel extends BaseModel {	
	
	
	/**
     * Run an update of the system from the SIMS database
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
            // only import the selected data if the SIMS snapshot can be found
            if(file_exists(SNAPSHOT_DIR . '/users_sfsuid.lst')) $this->importUsers();
            if(file_exists(SNAPSHOT_DIR . '/enroll_sfsuid.lst')) $this->importEnrollment();
            if(file_exists(SNAPSHOT_DIR . '/descrip_sfsuid.lst') && file_exists(SNAPSHOT_DIR . '/courses_sfsuid.lst')) $this->importSyllabi();
            
            // clean up orphaned syllabi
            // $this->deleteOrphans();
			
			// now, finally drop the snapshot_classes temp table
			$this->query= "DROP TABLE snapshot_classes";
			$this->executeQuery();
		    
            Messages::addMessage('The System was successfully updated', 'success');
            return true;
		}
	}


    /**
     * Import users into the system from the SIMS file
     */
    private function importUsers() {
        // build the temporary table		
        $this->query= "CREATE TEMPORARY TABLE `snapshot_users` (
            `SFSUid` INT( 9 ) NOT NULL ,
            `External_Person_Key` INT( 6 ) NOT NULL ,
            `User_ID` VARCHAR( 100 ) NOT NULL ,
            `Passwd` VARCHAR( 6 ) NOT NULL ,
            `Firstname` VARCHAR( 30 ) NOT NULL ,
            `Lastname` VARCHAR( 30 ) NOT NULL ,
            `Email` VARCHAR( 30 ) NOT NULL ,
            `Institution_Role` VARCHAR( 20 ) NOT NULL ,
            `System_Role` VARCHAR( 20 ) NOT NULL ,
            `Available_Ind` VARCHAR( 1 ) NOT NULL ,
            `Row_Status` VARCHAR( 10 ) NOT NULL
            ) TYPE = MYISAM ;";
        $this->executeQuery();
        
        // load into temporary table
        $snapshotFile = SNAPSHOT_DIR . '/users_sfsuid.lst';
        $this->query= "LOAD DATA LOCAL INFILE '".$snapshotFile."' INTO TABLE snapshot_users FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES";
        $this->executeQuery();
        
        // insert (update duplicates)
        $this->query = "INSERT INTO users (user_id, user_ext_id, user_fname, user_lname, user_email)  
            SELECT SFSUid, External_Person_Key, Firstname, Lastname, Email FROM snapshot_users WHERE Firstname != ''
            ON DUPLICATE KEY UPDATE user_fname=Firstname, user_lname=Lastname, user_email=Email;";
        $this->executeQuery();
        
        // drop temporary table		
        $this->query= "DROP TABLE snapshot_users";
        $this->executeQuery();
    }



    /**
     * Import the data into the enrollment table
     */
    private function importEnrollment() {
        // begin by truncating the enrollment table so we get a fresh set of data and no duplicates
        $this->query= "TRUNCATE enrollment;";
        $this->executeQuery();
        // build the temporary table
        $this->query= "CREATE TEMPORARY TABLE `snapshot_enroll` (
            `SFSUid` INT( 9 ) NOT NULL ,
            `External_Course_Key` VARCHAR( 13 ) NOT NULL ,
            `External_Person_Key` INT( 6 ) NOT NULL ,
            `Role` VARCHAR( 10 ) NOT NULL ,
            `Available_Ind` VARCHAR( 1 ) NOT NULL ,
            `Row_Status` VARCHAR( 10 ) NOT NULL
            ) TYPE = MYISAM ;";
        $this->executeQuery();
        
        // load into temporary table
        $snapshotFile = SNAPSHOT_DIR . '/enroll_sfsuid.lst';
        $this->query= "LOAD DATA LOCAL INFILE '".$snapshotFile."' INTO TABLE snapshot_enroll FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES";
        $this->executeQuery();
        
        // insert (update duplicates)
        $this->query= "INSERT INTO enrollment (enroll_user_id, enroll_class_id, enroll_role)  
            SELECT SFSUid, External_Course_Key, Role FROM snapshot_enroll
            ON DUPLICATE KEY UPDATE enroll_user_id=SFSUid, enroll_class_id=External_Course_Key, enroll_role=Role;";
        $this->executeQuery();
        
        // drop temporary table		
        $this->query= "DROP TABLE snapshot_enroll";
        $this->executeQuery();
    }
    
    
    
    /**
     * Import data into the syllabi table
     */
    private function importSyllabi() {
        // build the temporary table		
        $this->query= "CREATE TEMPORARY TABLE `snapshot_class_desc` (
            `External_Course_Key` VARCHAR( 13 ) NOT NULL ,
            `title` VARCHAR( 100 ) NOT NULL ,
            `units` TINYINT( 1 ) NOT NULL ,
            `catlg` INT( 5 ) NOT NULL ,
            `school` INT( 3 ) NOT NULL ,
            `dept` INT( 3 ) NOT NULL ,
            `computer_req` TINYINT( 1 ) NOT NULL ,
            `crs_description` TEXT NOT NULL ,
            `prereq_description` TEXT NOT NULL
            ) TYPE = MYISAM ;";
        $this->executeQuery();
        
        // load into temporary table
        $snapshotFile = (file_exists(SNAPSHOT_DIR . '/descrip_sfsuid.lst'))	?	 SNAPSHOT_DIR . '/descrip_sfsuid.lst'	:	SNAPSHOT_DIR . '/descrip.lst'	;
        $this->query= "LOAD DATA LOCAL INFILE '".$snapshotFile."' INTO TABLE snapshot_class_desc FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES";
        $this->executeQuery();
        
        // insert (update duplicates)
        $this->query= "INSERT INTO syllabus (syllabus_id, syllabus_class_schedule_number, syllabus_class_title, syllabus_class_description, syllabus_class_prereqs)  
            SELECT External_Course_Key, catlg, title, crs_description, prereq_description FROM snapshot_class_desc
            ON DUPLICATE KEY UPDATE syllabus_class_title=title, syllabus_class_description=crs_description, syllabus_class_prereqs=prereq_description";
        $this->executeQuery();
        
        // drop temporary table		
        $this->query= "DROP TABLE snapshot_class_desc";
        $this->executeQuery();
        
        
        /*
        Here we need to load a second snapshot file to get the rest of the necessary information for the syllabus table
        */
        
        // build the temporary table		
        $this->query= "CREATE TEMPORARY TABLE `snapshot_classes` (
            `External_Course_Key` VARCHAR( 13 ) NOT NULL ,
            `Course_ID` VARCHAR( 30 ) NOT NULL ,
            `Course_Name` VARCHAR( 100 ) NOT NULL ,
            `Available_Ind` VARCHAR( 1 ) NOT NULL ,
            `Row_Status` VARCHAR( 10 ) NOT NULL ,
            `Abs_Limit` INT( 6 ) NOT NULL ,
            `Upload_Limit` INT( 6 ) NOT NULL,
            PRIMARY KEY (`External_Course_Key`)
            ) ENGINE = MYISAM ;";
        $this->executeQuery();
        
        // load into temporary table
        $snapshotFile = SNAPSHOT_DIR . '/courses_sfsuid.lst';
        $this->query= "LOAD DATA LOCAL INFILE '".$snapshotFile."' INTO TABLE snapshot_classes FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES";
        $this->executeQuery();
        
        // insert (update duplicates)
        $hash_str = 'ai#9@LC8_2*';        
        $this->query= "INSERT INTO syllabus (syllabus_id, syllabus_class_number, syllabus_class_section, syllabus_class_semester, syllabus_class_year, syllabus_view_token )  
            SELECT 
                External_Course_Key,
                SUBSTR(Course_Name, 1, LOCATE('-', Course_Name, 1) - 1),
                SUBSTR(Course_Name, LOCATE('-', Course_Name, 1) + 1, 2), 
                SUBSTR(External_Course_Key, 5, 1),
                SUBSTR(External_Course_Key, 1, 4),
				MD5(CONCAT('" . $hash_str . "', External_Course_Key))
            FROM snapshot_classes
            ON DUPLICATE KEY UPDATE 
                syllabus_class_number=SUBSTR(Course_Name, 1, LOCATE('-', Course_Name, 1) - 1), 
                syllabus_class_section=SUBSTR(Course_Name, LOCATE('-',Course_Name, 1) + 1, 2), 
                syllabus_class_semester=SUBSTR(External_Course_Key, 5, 1),
                syllabus_class_year=SUBSTR(External_Course_Key, 1, 4),
				syllabus_view_token=MD5(CONCAT('" . $hash_str . "', External_Course_Key))
            ";
        $this->executeQuery();
        // Don't drop the temporary table yet. We will use it again at the end of the script to delete orphans
        
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


	
} // end class
