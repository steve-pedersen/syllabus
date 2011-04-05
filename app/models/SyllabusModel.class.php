<?php


/**
 * Syllabus Model
 */
class SyllabusModel extends BaseModel {	

    /**
     * Retrieve a syllabus
     * @param string $syllabus_id The unique id for the class that the syllabus belongs to
     * @return array Array of the query result
     */
	public function getSyllabusById($syllabus_id) {
		$is_draft = (preg_match('!^draft-!', $syllabus_id)) ? true : false;
		$this->query = sprintf("
			SELECT * FROM syllabus s
            %s
			WHERE s.syllabus_id='%s' %s
            LIMIT 1;",
            ($is_draft)
                ? " "
                : " INNER JOIN enrollment e ON s.syllabus_id=e.enroll_class_id INNER JOIN users u ON e.enroll_user_id=u.user_id ",
			$syllabus_id,
            ($is_draft)
                ? " "
                : " AND e.enroll_role='instructor' "
		);
		$result = $this->executeQuery();
		
		if($result['count'] > 0 && !$is_draft) {
			// fall back to defaults if no data available
			if(empty($result['data'][0]['user_preferred_name'])) {
				$result['data'][0]['user_preferred_name'] = $result['data'][0]['user_fname'] . ' ' . $result['data'][0]['user_lname'];
				$result['data'][0]['user_preferred_name'] = $result['data'][0]['user_fname'] . ' ' . $result['data'][0]['user_lname'];
			}
			if(empty($result['data'][0]['syllabus_instructor'])) {
				$result['data'][0]['syllabus_instructor'] = $result['data'][0]['user_preferred_name'];
				$result['data'][0]['syllabus_instructor'] = $result['data'][0]['user_preferred_name'];
			}
			if(empty($result['data'][0]['syllabus_office'])) {
				$result['data'][0]['syllabus_office'] = $result['data'][0]['user_office'];
				$result['data'][0]['syllabus_office'] = $result['data'][0]['user_office'];
			}
			if(empty($result['data'][0]['syllabus_phone'])) {
				$result['data'][0]['syllabus_phone'] = $result['data'][0]['user_phone'];
				$result['data'][0]['syllabus_phone'] = $result['data'][0]['syllabus_phone'];
			}
			if(empty($result['data'][0]['syllabus_email'])) {
				$result['data'][0]['syllabus_email'] = $result['data'][0]['user_email'];
				$result['data'][0]['syllabus_email'] = $result['data'][0]['user_email'];
			}
			if(empty($result['data'][0]['syllabus_website'])) {
				$result['data'][0]['syllabus_website'] = $result['data'][0]['user_website'];
				$result['data'][0]['syllabus_website'] = $result['data'][0]['user_website'];
			}
			if(empty($result['data'][0]['syllabus_fax'])) {
				$result['data'][0]['syllabus_fax'] = $result['data'][0]['user_fax'];
				$result['data'][0]['syllabus_fax'] = $result['data'][0]['user_fax'];
			}
		}
		
		return ($result['count'] > 0) ? $result['data'][0] : false;
	}
	
	
	/**
	 * Get the number of current syllabi that have been edited (modules added)
	 * @return int Number of active syllabi
	 */
	public function getSyllabusCount() {
		$this->query = "SELECT COUNT(*) FROM syllabus;";
		$syllabus_count = $this->executeQuery();
		
		return $syllabus_count['data'][0]['COUNT(*)'];
	}
	
	
	/**
	 * Get the number of current syllabi that have been edited (modules added)
	 * @return int Number of active syllabi
	 */
	public function getActiveSyllabusCount() {
		$this->query = "SELECT * FROM syllabus_modules GROUP BY syllabus_id;";
		$syllabi = $this->executeQuery();
		
		return (isset($syllabi) && is_array($syllabi)) ? count($syllabi) : 0;
	}
	
	
	/**
	 * Get the user id for the instructor of a course
	 * @param string $syllabus_id The syllabus (course) id number
	 * @return string Returns the user id of the user, boolean false on fail
	 */
	public function getSyllabusInstructor($syllabus_id) {
		Validate::syllabus_id($syllabus_id);
		$this->query = sprintf("SELECT * FROM enrollment WHERE enroll_class_id='%s' AND enroll_role='instructor';", $syllabus_id);
		$result = $this->executeQuery();
        return ($result['count'] > 0) ? $result['enroll_user_id'] : false;
	}
    
    
    /**
     * Get a syllabus by an item that it contains
     * @param string $module_type The type of module the item belongs to
     * @param int $item_id The id of the item
     * @return string The syllabus id
     */
    public function getSyllabusByItem($module_type, $item_id) {
        $this->query = sprintf(
            "SELECT * FROM %s a INNER JOIN syllabus s ON a.syllabus_id=s.syllabus_id WHERE %s=%d;",
            $this->all_modules[$module_type]['db_name'],
            strtolower($this->all_modules[$module_type]['singular']) . '_id',
            $item_id
            );
		$result = $this->executeQuery();
		return ($result['count'] == 1) ? $result['data'][0] : false;
    }
    
    
    /**
     * Get all editable and viewable syllabi for the specified user
     * @param int $user_id The User's id
     * @return array Return the result array
     */
    public function getSyllabiForUser($user_id = null) {
        if(!is_numeric($user_id) || strlen($user_id) != 9) {
            $user_id = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0;
        }
		
        $syllabi_array = array();
        
        // get the various syllabi
        $instructor_syllabi = $this->getInstructorSyllabi($user_id);
        $student_syllabi = $this->getStudentSyllabi($user_id);
        $editable_syllabi = $this->getEditableSyllabi($user_id);
        
        $syllabi_array = array_merge($instructor_syllabi, $student_syllabi, $editable_syllabi);
       
        $return_array = array();
        foreach($syllabi_array as $k => $v) {
                $y = $v['syllabus_class_year'];
                $s = $v['syllabus_class_semester'];
                $id = $v['syllabus_id'];
                $return_array[$y][$s][$id] = $v;
            if(!empty($v['syllabus_id'])) {
            }
        }
        return $return_array; 
    }


    /**
     * Get all drafts for the current user
     * @param int $user_id The user id
     * @return array Return all the draft syllabi for this user
     */
    public function getDraftsForUser($user_id = null) {
        $user_id = (!is_null($user_id) && is_numeric($user_id) || strlen($user_id) == 9)
            ? $user_id
            : $_SESSION['user_id'];
        $this->query = sprintf("SELECT * FROM syllabus WHERE syllabus_draft_owner='%s';", $user_id);
        $result = $this->executeQuery();
		return ($result['count'] > 0) ? $result['data'] : array();
    }

    /**
     * Get all syllabi for a particular instructor
     * @param int $user_id The user's unique id
     * @return array Returns an array of classes
     */
	public function getInstructorSyllabi($user_id = null) {
        if(!is_numeric($user_id) || strlen($user_id) != 9) {
            $user_id = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0;
        }
		
		$this->query= sprintf("
            SELECT * FROM enrollment e 
            INNER JOIN users u ON e.enroll_user_id=u.user_id
            INNER JOIN syllabus s ON e.enroll_class_id=s.syllabus_id
            WHERE e.enroll_user_id='%s' AND e.enroll_role='instructor' AND e.enroll_class_id REGEXP '^[0-9]{5}\-[A-Z]\-[0-9]{5}$'
            GROUP BY s.syllabus_id
            ORDER BY s.syllabus_class_year DESC, s.syllabus_class_semester DESC, s.syllabus_class_number ASC, s.syllabus_class_section ASC;",
			$user_id
		);
		
		$result = $this->executeQuery();
		return($result['count'] > 0) ? $result['data'] : array();
	}

    
    /**
     * Check if the user is the instructor for a class
     * @param string $class_id The class_id
     * @param int $user_id The user's id
     * @return bool Returns true if the user is the instructor, false otherwise
     */
    public function isClassInstructor($class_id, $user_id = null) {
        if(!is_numeric($user_id) || strlen($user_id) != 9) {
            $user_id = (isset($_SESSION['user_id']))
                ? $_SESSION['user_id']
                : 0;
        }
        $this->query = sprintf(
            "SELECT * FROM enrollment WHERE enroll_user_id='%s' AND enroll_class_id='%s' AND enroll_role='instructor';",
            $user_id,
            $class_id
            );
        $result = $this->executeQuery();
        return ($result['count'] > 0) ? true : false;
    }


    /**
     * Get all syllabi for a particular student
     * @param int $user_id The user's unique id
     * @return array Returns an array of classes
     */
	public function getStudentSyllabi($user_id = null) {
        if(!is_numeric($user_id) || strlen($user_id) != 9) {
            $user_id = (isset($_SESSION['user_id']))
                ? $_SESSION['user_id']
                : 0;
        }
		$this->query= sprintf("
            SELECT * FROM enrollment e
            INNER JOIN syllabus s ON e.enroll_class_id=s.syllabus_id
            INNER JOIN users u ON e.enroll_user_id=u.user_id
            WHERE e.enroll_user_id='%s' AND e.enroll_role='student' AND e.enroll_class_id REGEXP '^[0-9]{5}\-[A-Z]\-[0-9]{5}$'
            GROUP BY s.syllabus_id
            ORDER BY s.syllabus_class_year DESC, s.syllabus_class_semester DESC, s.syllabus_class_number ASC, s.syllabus_class_section ASC;",
			$user_id
		);
		
		$student_syllabi_result = $this->executeQuery();
		$student_syllabi = ($student_syllabi_result['count'] > 0) ? $student_syllabi_result['data'] : array();
		
		// get the actual instructor for the class and replace the information in the array
		foreach($student_syllabi as $key => $syllabus) {
			$result = $this->getSyllabusById($syllabus['syllabus_id']);
			if(is_array($result)) {
				foreach($result as $k => $v) {
					if(strpos($k, 'user_') !== false) {
						$student_syllabi[$key][$k] = $v;
					}
				}
			}
		}
		
        return $student_syllabi;
	}
    
    
    /**
     * Check if the user is a student registered in a class
     * @param string $class_id The class_id
     * @param int $user_id The user's id
     * @return bool Returns true if the user is a student, false otherwise
     */
    public function isClassStudent($class_id, $user_id = null) {
        if(!is_numeric($user_id) || strlen($user_id) != 9) {
            $user_id = (isset($_SESSION['user_id']))
                ? $_SESSION['user_id']
                : 0;
        }
        $this->query = sprintf(
            "SELECT * FROM enrollment WHERE enroll_user_id='%s' AND enroll_class_id='%s' AND enroll_role='student';",
            $user_id,
            $class_id
        );
		
        $result = $this->executeQuery();
        return ($result['count'] > 0) ? true : false;
    }


    /**
     * Get all syllabi the user has been granted permission to edit
     * @param int $user_id User's id
     * @return array Array of editable syllabi
     */
    public function getEditableSyllabi($user_id = null) {
        if(!is_numeric($user_id) || strlen($user_id) != 9) {
            $user_id = (isset($_SESSION['user_id']))
                ? $_SESSION['user_id']
                : 0;
        }
        $editableSyllabi = array();
        // get any classes the user has been granted edit permission to
        $this->query = sprintf("
            SELECT * FROM permissions p
            INNER JOIN syllabus s ON p.syllabus_id=s.syllabus_id
            INNER JOIN users u ON p.user_id=u.user_id
            WHERE p.user_id='%s' AND p.permission='edit_syllabus' AND p.syllabus_id IS NOT NULL;",
            $user_id
        );
		
		$courses = $this->executeQuery();
		$return = array();
		if($courses['count'] > 0) {
			foreach($courses['data'] as $k => $v) {
				if(false != ($syllabus = $this->getSyllabusById($v['syllabus_id']))) {
					$return[] = $syllabus;
				}
			}
		}
		
		return $return;
    }


    /**
     * Get all modules currently enabled for a syllabus
     * @param string $syllabus_id The id of the syllabus to retrieve the modules for
     * @return array Returns an array of the machine readable names of enabled modules for the syllabus
     */
    public function getModulesForSyllabus($syllabus_id) {
        $this->query = sprintf("SELECT * FROM syllabus_modules WHERE syllabus_id='%s' ORDER BY module_order ASC;", $syllabus_id);
        $result = $this->executeQuery();
        $modules = array();
		
		if(isset($result['data']) && is_array($result['data'])) {
			foreach($result['data'] as $k => $v) {
				$modules[$v['module_type']] = $v;
			}
		}
		
        return $modules;
    }

    
    /**
     * Get the custom name of a module for a particular syllabus
     * @param string $syllabus_id Unique id of the syllabus
     * @param string $module_type Module type
     * @return string Custom name of the module
     */
    public function getCustomModuleName($syllabus_id, $module_type) {
        $this->query = sprintf("SELECT * FROM syllabus_modules WHERE syllabus_id='%s' AND module_type='%s';", $syllabus_id, $module_type);
        $result = $this->executeQuery();
        return $result['data'][0]['module_custom_name'];
    }
    
    
    /**
     * Get any users who have been granted edit permission to the syllabi
     * @param string $syllabus_id The id of the syllabus
     * @return array Returns an array of users
     */
    public function getEditorsForSyllabus($syllabus_id) {
        $this->query = sprintf("
            SELECT * FROM permissions p
            INNER JOIN users u ON p.user_id=u.user_id
            WHERE syllabus_id='%s' AND permission='edit_syllabus'
            ORDER BY u.user_lname, u.user_fname ASC;",
            $syllabus_id
        );
		
		$result = $this->executeQuery();
		return ($result['count'] > 0) ? $result['data'] : array();
    }


    /**
     * Get all Assignments for a particular syllabus
     * @return array Returns an array of the results
     */
	public function getAssignmentsForSyllabus($syllabus_id) {
		$this->query= sprintf("SELECT * FROM assignments WHERE syllabus_id='%s' ORDER BY assignment_order ASC;", $syllabus_id);
		$result = $this->executeQuery();
		return ($result['count'] > 0) ? $result['data'] : array();
	}


    /**
     * Get all Materials for a particular syllabus
     * @return array Returns an array of the results
     */
	public function getMaterialsForSyllabus($syllabus_id) {
		$this->query= sprintf("SELECT * FROM materials WHERE syllabus_id='%s' ORDER BY material_order ASC;", $syllabus_id);
		$result = $this->executeQuery();
		return ($result['count'] > 0) ? $result['data'] : array();
	}


    /**
     * Get all Methods for a particular syllabus
     * @return array Returns an array of the results
     */
	public function getMethodsForSyllabus($syllabus_id) {
		$this->query= sprintf("SELECT * FROM methods WHERE syllabus_id='%s' ORDER BY method_order ASC;", $syllabus_id);
		$result = $this->executeQuery();
		return ($result['count'] > 0) ? $result['data'] : array();
	}


    /**
     * Get all Objectives for a particular syllabus
     * @return array Returns an array of the results
     */
	public function getObjectivesForSyllabus($syllabus_id) {
		$this->query= sprintf("SELECT * FROM objectives WHERE syllabus_id='%s' ORDER BY objective_order ASC;", $syllabus_id);
		$result = $this->executeQuery();
		return ($result['count'] > 0) ? $result['data'] : array();
	}


    /**
     * Get all Policies for a particular syllabus
     * @return array Returns an array of the results
     */
	public function getPoliciesForSyllabus($syllabus_id) {
		$this->query= sprintf("SELECT * FROM policies WHERE syllabus_id='%s' ORDER BY policy_order ASC;", $syllabus_id);
		$result = $this->executeQuery();
		return ($result['count'] > 0) ? $result['data'] : array();
	}


    /**
     * Get all Schedules for a particular syllabus
     * @return array Returns an array of the results
     */
	public function getSchedulesForSyllabus($syllabus_id) {
		$this->query= sprintf("SELECT * FROM schedules WHERE syllabus_id='%s' ORDER BY schedule_order ASC;", $syllabus_id);
		$result = $this->executeQuery();
		return ($result['count'] > 0) ? $result['data'] : array();
	}


    /**
     * Get all Teaching Assistants for a particular syllabus
     * @return array Returns an array of the results
     */
	public function getTasForSyllabus($syllabus_id) {
		$this->query= sprintf("SELECT * FROM teaching_assistants WHERE syllabus_id='%s' ORDER BY ta_order ASC;", $syllabus_id);
		$result = $this->executeQuery();
		return ($result['count'] > 0) ? $result['data'] : array();
	}


    /**
     * Wrapper function to get a specific module item
     * @param string $module_type The type of module the item belongs to
     * @param int $item_id The id of the item
     * @return array Returns an array of the item on success, false otherwise
     */
    public function getModuleItem($module_type, $item_id) {
        switch($module_type) {
            case 'assignments': $return = $this->getAssignmentById($item_id); break;
            case 'materials': $return = $this->getMaterialById($item_id); break;
            case 'methods': $return = $this->getMethodById($item_id); break;
            case 'objectives': $return = $this->getObjectiveById($item_id); break;
            case 'policies': $return = $this->getPolicyById($item_id); break;
            case 'schedules': $return = $this->getScheduleById($item_id); break;
            case 'tas': $return = $this->getTaById($item_id); break;
            default: $return = false; break;
        }
        return $return;
    }

    
    /**
     * Get a specific assignment
     * @param int $id The assignment id
     * @return array Returns the result array
     */
    public function getAssignmentById($id) {
        $this->query = sprintf("SELECT * FROM assignments WHERE assignment_id=%d;", $id);
        $result = $this->executeQuery();
		return ($result['count'] == 1) ? $result['data'][0] : false;
    }

    
    /**
     * Get a specific material
     * @param int $id The material id
     * @return array Returns the result array
     */
    public function getMaterialById($id) {
        $this->query = sprintf("SELECT * FROM materials WHERE material_id=%d;", $id);
        $result = $this->executeQuery();
		return ($result['count'] == 1) ? $result['data'][0] : false;
    }

    
    /**
     * Get a specific method
     * @param int $id The method id
     * @return array Returns the result array
     */
    public function getMethodById($id) {
        $this->query = sprintf("SELECT * FROM methods WHERE method_id=%d;", $id);
        $result = $this->executeQuery();
		return ($result['count'] == 1) ? $result['data'][0] : false;
    }

    
    /**
     * Get a specific objective
     * @param int $id The objective id
     * @return array Returns the result array
     */
    public function getObjectiveById($id) {
        $this->query = sprintf("SELECT * FROM objectives WHERE objective_id=%d;", $id);
        $result = $this->executeQuery();
		return ($result['count'] == 1) ? $result['data'][0] : false;
    }

    
    /**
     * Get a specific policy
     * @param int $id The policy id
     * @return array Returns the result array
     */
    public function getPolicyById($id) {
        $this->query = sprintf("SELECT * FROM policies WHERE policy_id=%d;", $id);
        $result = $this->executeQuery();
		return ($result['count'] == 1) ? $result['data'][0] : false;
    }

    
    /**
     * Get a specific schedule
     * @param int $id The schedules id
     * @return array Returns the result array
     */
    public function getScheduleById($id) {
        $this->query = sprintf("SELECT * FROM schedules WHERE schedule_id=%d;", $id);
        $result = $this->executeQuery();
		return ($result['count'] == 1) ? $result['data'][0] : false;
    }

    
    /**
     * Get a specific teaching assistant
     * @param int $id The ta id
     * @return array Returns the result array
     */
    public function getTaById($id) {
        $this->query = sprintf("SELECT * FROM teaching_assistants WHERE ta_id=%d;", $id);
        $result = $this->executeQuery();
		return ($result['count'] == 1) ? $result['data'][0] : false;
    }
	
    
    /**
     * Register a view token to give the user permission to view the syllabus for the duration of the session
     */
    public function registerViewToken($syllabus_id, $token) {
        $this->query = sprintf("SELECT * FROM syllabus WHERE syllabus_id='%s' AND syllabus_view_token='%s';", $syllabus_id, $token);
        $result = $this->executeQuery();
        if($result['count'] == 1) {
			$syllabus_id = $result['data'][0]['syllabus_id'];
            $_SESSION['allow_temporary_view'][$syllabus_id] = true;
			Utility::redirect('syllabus/view/' . $syllabus_id);
        } else {
            Messages::addMessage('Invalid view token.', 'error');
            $return = false;
        }
		
        return $return;
    }
    
    
    /**
     * Set the visibility for a syllabus
     * @return bool Returns true on success, false otherwise
     */
    public function setSyllabusVisibility() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            $this->query = sprintf("UPDATE syllabus SET syllabus_visibility='%s' WHERE syllabus_id='%s';",
                $this->syllabus_visibility,
                $this->syllabus_id
            );
            $this->executeQuery();
            Messages::addMessage('View permissions updated.', 'success');
			$this->redirect = 'syllabus/share/' . $this->syllabus_id;
            $return = true;
        } else {
            Messages::addMessage('You do not have permission to edit the selected syllabus.', 'error');
            $return = false;
        }
		
        return $return;
    }
    
    
    /**
     * Reset the view password for the syllabus
     */
    public function resetToken() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            $token = md5('#Dk32_!p' . $this->syllabus_id . time());
            $this->query = sprintf("UPDATE syllabus SET syllabus_view_token='%s' WHERE syllabus_id='%s';", $token, $this->syllabus_id);
            $this->executeQuery();
            Messages::addMessage('Syllabus password successfully reset.', 'success');
			$this->redirect = 'syllabus/share/' . $this->syllabus_id;
            $return = true;
        } else {
            $this->error_messages[] = 'You do not have permission to reset the password for the selected syllabus.';
            $return = false;
        }
		
        return $return;
    }
    
    
    /**
     * Send an invitation email
     */
    public function sendInvites() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            $S = new SyllabusModel();
            $syllabus = $S->getSyllabusById($this->syllabus_id);
			
			// remove \r\n which get added via BaseModel::__set() and the mysqli->real_escape_string()
			// this is pretty hackish ... need to change the sanitize so that it only runs before DB entry or have a way
			// to prevent fields from being escaped
			$this->invite_message = preg_replace('!\\\r\\\n!', '', $this->invite_message);
			
            $Mailer = new Mailer();
            $Mailer->setTo($this->to);
            $Mailer->setFrom('Syllabus @ SF State <no-reply@syllabus.sfsu.edu>');
            $Mailer->setReplyTo('Syllabus @ SF State <no-reply@syllabus.sfsu.edu>');
            $Mailer->setBcc($this->invite_addresses);
            $Mailer->setSubject('You have been invited to view a Syllabus');
            $Mailer->setBody(
                '<div>' . $this->invite_message . '</div>
                <hr style="margin: 1em 0px;" />        
                <h2>You\'ve been invited to view a syllabus</h2>
                <p>' .
                $_SESSION['user_fname'] . ' ' . $_SESSION['user_lname'] . ' has invited you to view the syllabus for <strong>' .
                $syllabus['syllabus_class_number'] . ' &ndash; ' . str_pad($syllabus['syllabus_class_section'], 2, '0', 'left') .
                '</strong> on the Syllabus tool website.  You can access the syllabus by clicking on the link below.  Please note that you
                will only be granted temporary access.  If your session expires, you will need to click the link again to restart your
                session and regain access.
                </p>
                <p>
                <a href="' . BASEHREF . 'syllabus/view/' . $syllabus['syllabus_id'] . '?token=' . $syllabus['syllabus_view_token'] . '" target="_blank">
                ' . BASEHREF . 'syllabus/view/' . $syllabus['syllabus_id'] . '?token=' . $syllabus['syllabus_view_token'] . '
                </a>
                </p>
                
                <p>Happy Viewing!</p>
                <p><strong>The Syllabus Team</strong></p>
                '                    
            );
            
            if(count($Mailer->getErrors())) {
                $return = false;
                foreach($Mailer->getErrors() as $k => $v) {
					Messages::addMessage($v, 'error');
                }
            } else {
                if($Mailer->sendMail()) {
                    $return = true;
                    Messages::addMessage('Your invitations have been sent successfully.', 'success');
					$this->redirect = 'syllabus/share/' . $this->syllabus_id;
                } else {
                    Messages::addMessage('An unknown error occurred while attmepting to send your email.', 'error');
                    $return = false;
                }
            }
            
        } else {
            Messages::addMessage('You do not have permission to invite people to this syllabus.', 'error');
            $return = false;
        }
		
        return $return;
    }
    

    /**
     * Enable a module for the current syllabus
     * @return bool Returns true if successful, false if there is an error
     */
	public function addModule() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            if(isset($this->all_modules[$this->module_type])) {
                $current_modules = $this->getModulesForSyllabus($this->syllabus_id);
                $count = count($current_modules)+2;
                if(!isset($current_modules[$this->module_type])) {
                    $this->query = sprintf(
                        "INSERT INTO syllabus_modules (syllabus_id, module_type, module_order, module_custom_name) VALUES ('%s','%s',%d,'%s');",
                        $this->syllabus_id,
                        $this->module_type,
                        $count,
                        !empty($this->module_custom_name)
                            ? $this->module_custom_name
                            : $this->all_modules[$this->module_type]['name']
                    );
                    $this->executeQuery();
					Messages::addMessage('The module was successfully added', 'success');
					$this->redirect = 'syllabus/edit/' . $this->syllabus_id;
                    $return = true;
                } else {
                    Message::addMessage('The selected module is already enabled for this syllabus', 'success');
					$this->redirect = 'syllabus/edit/' . $this->syllabus_id;
                    $return = true;
                }
            } else {
                Messages::addMessage('Invalid module.  Please select a module from the list to add it.', 'error');
                $return = false;
            }
        } else {
            Messages::addMessage('You do not have permission to edit the selected syllabus.', 'error');
            $return = false;
        }
		
        return $return;
	}
    

    /**
     * Edit an enabled module
     * @return bool Returns true if successful, false if there is an error
     */
	public function editModule() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            if(isset($this->all_modules[$this->module_type])) {
                $this->query = sprintf(
                    "UPDATE syllabus_modules SET module_custom_name='%s' WHERE syllabus_id='%s' AND module_type='%s';", 
                    $this->module_custom_name,
                    $this->syllabus_id,
                    $this->module_type
				);
                $this->executeQuery();
                Messages::addMessage('The module was successfully edited', 'success');
				$this->redirect = 'syllabus/edit/' . $this->syllabus_id;
                $return = true;
            } else {
                Messages::addMessage('Invalid module.', 'error');
                $return = false;
            }
        } else {
            Messages::addMessage('You do not have permission to edit the selected syllabus.', 'error');
            $return = false;
        }
		
        return $return;
	}
	

    /**
     * Save the order of the syllabus modules and their respecive items
     * @return bool Returns true if successful, false if there is an error
     */
	public function saveOrder() {
		
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            if(is_array($this->sort_modules)) {
                foreach($this->sort_modules as $k => $v) {
                    $this->query = sprintf("UPDATE syllabus_modules SET module_order=%d WHERE syllabus_id='%s' AND module_type='%s';",
                        $v,
                        $this->syllabus_id,
                        ($k)
                        );
                    $this->executeQuery();
                    
                }
            }
            
            if(is_array($this->sort_items)) {
                foreach($this->sort_items as $module => $items_array) {
                    foreach($items_array as $k => $v) {
                        $db_table = $this->all_modules[$module]['db_name'];
                        $db_prefix = $this->all_modules[$module]['db_prefix'];
                        $this->query = sprintf("UPDATE %s SET %s_order=%d WHERE syllabus_id='%s' AND %s_id=%d;",
                            $db_table,
                            $db_prefix,
                            $v,
                            $this->syllabus_id,
                            $db_prefix,
                            $k                            
                        );
                        $this->executeQuery();
                    }
                }
            }
            
            Messages::addMessage('The syllabus was successfully saved', 'success');
			$this->redirect = 'syllabus/edit/' . $this->syllabus_id;
            $return = true;
        } else {
            Messages::addMessage('You do not have permission to edit the selected syllabus', 'error');
            $return = false;
        }
		
        return $return;
	}
    
    
    /**
     * Remove a module from the syllabus
     * @return bool Return true if the module was successfully removed, false if there was an error
     */
	public function removeModule() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            $this->query = sprintf("
                DELETE FROM syllabus_modules WHERE syllabus_id='%s' AND module_type='%s';",
                $this->syllabus_id,
                $this->module_type
            );
            $this->executeQuery();
            
            /*
            // This code can be commented if we do not want to remove all module data when the module is removed frm the syllabus
            // With the code commented, removing a module simply hides it but does not delete any data so that if the module is
            // re-enabled, it will still have all the old data
            */
            $this->query = sprintf(
                "DELETE FROM " . $this->all_modules[$this->module_type]['db_name'] . " WHERE syllabus_id='%s';",
                $this->syllabus_id
            );
            $this->executeQuery();
            
            $S = new SyllabusModel;
            $current_modules = $S->getModulesForSyllabus($this->syllabus_id);
            $i = 1;
            foreach($current_modules as $k => $v) {
                $this->query = sprintf(
                    "UPDATE syllabus_modules SET module_order=%d WHERE syllabus_id='%s' AND module_type='%s';",
                    $i,
                    $this->syllabus_id,
                    $v['module_type']
                );
                $this->executeQuery();
                $i++;
            }
            
            Messages::addMessage('The <strong>' . $this->module_type . '</strong> module was successfully removed.', 'success');
			$this->redirect = 'syllabus/edit/' . $this->syllabus_id;
            $return = true;
            
        } else {
            Messages::addMessage('You do not have permission to edit the selected syllabus.', 'error');
            $return = false;
        }
		
        return $return;
	}
    
    
    /**
     * Add an editor to the syllabus
     * @return bool Returns true if successful, false otherwise
     */
    public function addSyllabusEditor() {
		$syllabus_id = $this->syllabus_id;
		
        if($this->Permissions->canEditSyllabus($syllabus_id)) {
            $this->query = sprintf("SELECT * FROM users WHERE user_email='%s';", $this->user_email);
            $result = $this->executeQuery();
            if($result['count'] == 1) {
                $user_id = $result['data'][0]['user_id'];
                $user_fname = $result['data'][0]['user_fname'];
                $user_lname = $result['data'][0]['user_lname'];
                $this->query = sprintf("
                    SELECT * FROM permissions p WHERE syllabus_id='%s' AND user_id='%s' AND permission='edit_syllabus';",
                    $syllabus_id,
                    $user_id
                );
                $result = $this->executeQuery();
                if($result['count'] == 0)  {
                    $this->query = sprintf(
                        "INSERT INTO permissions (user_id,permission,syllabus_id) VALUES ('%s', 'edit_syllabus', '%s')",
                        $user_id,
                        $syllabus_id
                    );
                    $this->executeQuery();
                    Messages::addMessage($user_fname . ' ' . $user_lname . ' was successfully added as an editor.', 'success');
					$this->redirect = 'syllabus/share/' . $syllabus_id;
                    $return = true;
                } else {
                    Messages::addMessage('The selected user (' . $this->user_fname . ' ' . $this->user_lname . ') is already an editor for this syllabus.', 'success');
                    $return = true;
                }
            } else {
                Messages::addMessage('There is no user with the entered email address (' . $this->user_email . ').', 'error');
                $return = false;
            }
        } else {
            Messages::addMessage('You do not have permission to edit this syllabus.', 'error');
            $return = false;
        }
		
        return $return;
    }
    
    
    /**
     * Remove editors from a syllabus
     * @return bool Returns true if successful, false otherwise
     */
    public function removeSyllabusEditors() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            if(is_array($this->remove_editors)) {
                foreach($this->remove_editors as $k => $v) {
                    $this->query = sprintf("DELETE FROM permissions WHERE user_id='%s' AND permission='edit_syllabus' AND syllabus_id='%s';", $v, $this->syllabus_id);
                    $this->executeQuery();
                }
                Messages::addMessage('The selected editors were successfully removed.', 'success');
				$this->redirect = 'syllabus/share/' . $this->syllabus_id;
                $return = true;
            } else {
                Messages::addMessage('You did not select any editors to remove.', 'error');
                $return = false;
            }
        } else {
            Messages::addMessage('You do not have permission to edit this syllabus.', 'error');
            $return = false;
        }
		
        return $return;
    }
    
    
    /**
     * Save the general information for a syllabus
     * @return bool Returns true if successful, false otherwise
     */
    public function editSyllabus() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            $this->query = sprintf("
                UPDATE syllabus SET
                syllabus_class_description='%s',
                syllabus_instructor='%s',
                syllabus_phone='%s',
                syllabus_email='%s',
                syllabus_office='%s',
                syllabus_office_hours='%s',
                syllabus_mobile='%s',
                syllabus_fax='%s'
                WHERE syllabus_id='%s'
                ;",
                $this->syllabus_class_description,
                $this->syllabus_instructor,
                Utility::formatPhoneNumber($this->syllabus_phone),
                $this->syllabus_email,
                $this->syllabus_office,
                $this->syllabus_office_hours,
                Utility::formatPhoneNumber($this->syllabus_mobile),
                Utility::formatPhoneNumber($this->syllabus_fax),
                $this->syllabus_id
			);
			
            $this->executeQuery();
            Messages::addMessage('Syllabus Information saved.', 'success');
			$this->redirect = 'syllabus/edit/' . $this->syllabus_id;
            $return = true;
        } else {
            Messages::addMessage('You do not have permission to edit this syllabus.', 'error');
            $return = false;
        }
		
        return $return;
    }
    
    
    /**
     * Create a draft syllabus
     * @return bool Returns true if successful, false otherwise
     */
    public function createDraft() {
        if($this->Permissions->hasPermission(PERM_DRAFTS)) {
            $this->query = sprintf("
                INSERT INTO syllabus SET
                syllabus_id='%s',
                syllabus_draft_owner='%s',
                syllabus_class_description='%s',
                syllabus_class_number='Draft',
                syllabus_class_title='%s',
                syllabus_instructor='%s',
                syllabus_phone='%s',
                syllabus_email='%s',
                syllabus_office='%s',
                syllabus_office_hours='%s',
                syllabus_mobile='%s',
                syllabus_fax='%s',
                syllabus_view_token='%s'
                ;",
                $this->syllabus_id,
                $_SESSION['user_id'],
                $this->syllabus_class_description,
                $this->syllabus_class_title,
                $this->syllabus_instructor,
                $this->syllabus_phone,
                $this->syllabus_email,
                $this->syllabus_office,
                $this->syllabus_office_hours,
                $this->syllabus_mobile,
                $this->syllabus_fax,
                md5('draft-' . $_SESSION['user_id'] . '-' . time())                
            );
			
            $this->executeQuery();
            Messages::addMessage('Draft Created.', 'success');
			$this->redirect = 'syllabus';
            $return = true;
        } else {
            Messages::addMessage('You do not have permission to create drafts.', 'error');
            $return = false;
        }
        return $return;
    }
    
    
    /**
     * Delete draft(s)
     * @return bool Returns true on success, false otherwise
     */
    public function deleteDrafts() {
        if(isset($this->drafts) && is_array($this->drafts) && count($this->drafts)) {
            $S = new SyllabusModel();
            foreach($this->drafts as $k => $draft) {
                $syllabus = $S->getSyllabusById($draft);
                if($this->Permissions->isAdmin() || $S->syllabus_draft_owner == $_SESSION['user_id']) {
                    $this->query = sprintf("DELETE FROM syllabus WHERE syllabus_id='%s';", $draft);
                    $this->executeQuery();
					$this->redirect = 'syllabus';
					Messages::addMessage('<strong>' . $syllabus['syllabus_class_title'] . '</strong> successfully deleted.', 'success');
					$return = true;
                } else {
                    Messages::addMessage('You do not have permission to delete the draft: ' . $syllabus['syllabus_class_title'], 'error');
					$return = false;
                }
            }
        } else {
            Messages::addMessage('Please select at least one draft to delete.', 'error');
            $return = false;
        }
        
        return $return;
    }
	
	
	/**
     * Add a new item to a specific module
     * @return bool Returns true on success, false otherwise
     */
	public function addItem() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {   
            $A = new SyllabusModel();
            $get_method = 'get' . ucwords(strtolower($this->module_type)) . 'ForSyllabus';
            $items = $A->{$get_method}($this->syllabus_id);
            $i = count($items) + 1;
            switch($this->module_type) {
                
                case 'assignments':
                    $this->query = sprintf(
                        "INSERT INTO assignments (syllabus_id, assignment_title, assignment_desc, assignment_value, assignment_order) VALUES ('%s','%s','%s', '%s', %d);",
                        $this->syllabus_id,
                        $this->assignment_title,
                        $this->assignment_desc,
                        $this->assignment_value,
                        $i
                        );
                break;
                
                case 'materials':
                    $this->query = sprintf(
                        "INSERT INTO materials (syllabus_id, material_title, material_info, material_required, material_order) VALUES ('%s','%s','%s', %d, %d);",
                        $this->syllabus_id,
                        $this->material_title,
                        $this->material_info,
                        $this->material_required,
                        $i
                        );
                break;
                
                case 'methods':
                    $this->query = sprintf(
                        "INSERT INTO methods (syllabus_id, method_title, method_text, method_order) VALUES ('%s', '%s', '%s', %d);",
                        $this->syllabus_id,
                        $this->method_title,
                        $this->method_text,
                        $i
                        );
                break;
                
                case 'objectives':
                    $this->query = sprintf(
                        "INSERT INTO objectives (syllabus_id, objective_title, objective_text, objective_order) VALUES ('%s', '%s', '%s', %d);",
                        $this->syllabus_id,
                        $this->objective_title,
                        $this->objective_text,
                        $i
                        );
                break;
                
                case 'policies':
                    $this->query = sprintf(
                        "INSERT INTO policies (syllabus_id, policy_title, policy_text, policy_order) VALUES ('%s', '%s', '%s', %d);",
                        $this->syllabus_id,
                        $this->policy_title,
                        $this->policy_text,
                        $i
                        );
                break;
                
                case 'schedules':
                    $this->query = sprintf(
                        "INSERT INTO schedules (syllabus_id, schedule_date, schedule_period, schedule_desc, schedule_due, schedule_order) VALUES ('%s', '%s', '%s', '%s', '%s', %d);",
                        $this->syllabus_id,
                        $this->schedule_date,
						$this->schedule_period,
                        $this->schedule_desc,
                        $this->schedule_due,
                        $i
                        );
                break;
                
                case 'tas':
                    $this->query = sprintf(
                        "INSERT INTO teaching_assistants (syllabus_id, ta_name, ta_email, ta_order) VALUES ('%s', '%s', '%s', %d);",
                        $this->syllabus_id,
                        $this->ta_name,
                        $this->ta_email,
                        $i
                        );                   
                break;
                
                default: break;		
            }
			
			if(isset($this->query) && !empty($this->query)) {
				$this->executeQuery();
				Messages::addMessage('The new item has been added', 'success');
				$this->redirect = 'syllabus/edit/' . $this->syllabus_id;
				$return = true;
			} else {
				Messages::addMessage('Invalid module type', 'error');
				$return = false;
			}
            
        } else {
            Messages::addMessage('You do not have permission to edit this syllabus.', 'error');
            $return = false;
        }
		
        return $return;
	}
	
	
	/**
     * Add a new item from the repository
     * @return bool Returns true on success, false otherwise
     */
	public function addItemFromRepository() {
		if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
			$module = $this->all_modules[$this->module_type];
			$syllabus_id = $this->syllabus_id;
            $get_method = 'get' . ucwords(strtolower($this->module_type)) . 'ForSyllabus';
			$items = $this->{$get_method}($syllabus_id);
			$i = count($items) + 1;
			
			foreach($this->add_ids as $k => $v) {
                switch($this->module_type) {
                    
                    case 'assignments':
                        $this->query = sprintf(
                            "INSERT INTO assignments (syllabus_id, assignment_title, assignment_desc, assignment_value, assignment_order)
                            SELECT '%s', assignment_title, assignment_desc, assignment_value, %d
                            FROM assignments WHERE assignment_id=%d;",
                            $this->syllabus_id,
                            $i,
                            $v
                            );
                    break;
                    
                    case 'materials':
                        $this->query = sprintf(
                            "INSERT INTO materials (syllabus_id, material_title, material_info, material_required, material_order)
                            SELECT '%s', material_title, material_info, material_required, %d
                            FROM materials WHERE material_id=%d;",
                            $this->syllabus_id,
                            $i,
                            $v
                            );
                    break;
                    
                    case 'methods':
                        $this->query = sprintf(
                            "INSERT INTO methods (syllabus_id, method_title, method_text, method_order)
                            SELECT '%s', method_title, method_text, %d
                            FROM methods WHERE method_id=%d;",
                            $this->syllabus_id,
                            $i,
                            $v
                            );
                    break;
                    
                    case 'objectives':
                        $this->query = sprintf(
                            "INSERT INTO objectives (syllabus_id, objective_title, objective_text, objective_order)
                            SELECT '%s', objective_title, objective_text, %d
                            FROM objectives WHERE objective_id=%d;",
                            $this->syllabus_id,
                            $i,
                            $v
                            );
                    break;
                    
                    case 'policies':
                        $this->query = sprintf(
                            "INSERT INTO policies (syllabus_id, policy_title, policy_text, policy_order)
                            SELECT '%s', policy_title, policy_text, %d
                            FROM policies WHERE policy_id=%d;",
                            $this->syllabus_id,
                            $i,
                            $v
                            );
                    break;
                    
                    case 'schedules':
                        $this->query = sprintf(
                            "INSERT INTO schedules (syllabus_id, schedule_date, schedule_period, schedule_desc, schedule_due, schedule_order)
                            SELECT '%s',  schedule_date, schedule_period, schedule_desc, schedule_due, %d
                            FROM schedules WHERE schedule_id=%d;",
                            $this->syllabus_id,
                            $i,
                            $v
                            );
                    break;
                    
                    case 'tas':
                        $this->query = sprintf(
                            "INSERT INTO teaching_assistants (syllabus_id, ta_name, ta_email, ta_order)
                            SELECT '%s', ta_name, ta_email, %d
                            FROM teaching_assistants WHERE ta_id=%d;",
                            $this->syllabus_id,
                            $i,
                            $v
                            );                   
                    break;
                }
				
				$i++;
			}
			
			if(isset($this->query) && !empty($this->query)) {
				$this->executeQuery();
				Messages::addMessage('The repository item has been added', 'success');
				$this->redirect = 'syllabus/edit/' . $this->syllabus_id;
				$return = true;
			} else {
				Messages::addMessage('Invalid module type', 'error');
				$return = false;
			}
			
		} else {
			Messages::addMessage('You do not have permission to edit this syllabus', 'error');
		}
		
        return $return;
	}
    
    
    /**
     * Wrapper function to edit a specific item
     * @return bool Returns true on success, false otherwise
     */
    public function editItem() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            switch($this->module_type) {
                case 'assignments': 
                    $this->query = sprintf(
                        "UPDATE assignments SET assignment_title='%s', assignment_desc='%s', assignment_value='%s' WHERE assignment_id=%d;",
                        $this->assignment_title,
                        $this->assignment_desc,
                        $this->assignment_value,
                        $this->item_id
                    );
                break;
                
                case 'materials':
                    $this->query = sprintf(
                        "UPDATE materials SET material_title='%s', material_info='%s', material_required=%d WHERE material_id=%d;",
                        $this->material_title,
                        $this->material_info,
                        ($this->material_required == 1) ? 1 : 0,
                        $this->item_id
                    );
                break;
                
                case 'methods':
                    $this->query = sprintf(
                        "UPDATE methods SET method_title='%s', method_text='%s' WHERE method_id=%d;",
                        $this->method_title,
                        $this->method_text,
                        $this->item_id
                    );
                break;
             
                case 'objectives':
                    $this->query = sprintf(
                        "UPDATE objectives SET objective_title='%s', objective_text='%s' WHERE objective_id=%d;",
                        $this->objective_title,
                        $this->objective_text,
                        $this->item_id
                    );
                break;
             
                case 'policies':
                    $this->query = sprintf(
                        "UPDATE policies SET policy_title='%s', policy_text='%s' WHERE policy_id=%d;",
                        $this->policy_title,
                        $this->policy_text,
                        $this->item_id
                    );
                break;
                
                case 'schedules':
                    $this->query = sprintf(
                        "UPDATE schedules SET schedule_date='%s', schedule_period='%s', schedule_desc='%s', schedule_due='%s' WHERE schedule_id=%d",
                        $this->schedule_date,
                        $this->schedule_period,
                        $this->schedule_desc,
                        $this->schedule_due,
                        $this->item_id
                    );
                break;
                
                case 'tas':
                    $this->query = sprintf(
                        "UPDATE teaching_assistants SET ta_name='%s', ta_email='%s' WHERE ta_id=%d",
                        $this->ta_name,
                        $this->ta_email,
                        $this->item_id
                    );
                break;
            }
			
			if(isset($this->query) && !empty($this->query)) {
				$this->executeQuery();
				Messages::addMessage('The item has been edited', 'success');
				$this->redirect = 'syllabus/edit/' . $this->syllabus_id;
				$return = true;
			} else {
				Messages::addMessage('Invalid module type', 'error');
				$return = false;
			}
        } else {
            Messages::addMessage('You do not have permission to edit this syllabus.', 'error');
            $return = false;
        }
		
        return $return;
    }


    /**
     * Remove an item from a syllabus
     * @return bool returns true on success, false otherwise
     */
    public function removeItem() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            $this->query = sprintf(
                "DELETE FROM %s WHERE %s=%d;",
                $this->all_modules[$this->module_type]['db_name'],
                $this->all_modules[$this->module_type]['db_prefix'] . '_id',
                $this->item_id
                );
            $this->executeQuery();
			$this->redirect = 'syllabus/edit/' . $this->syllabus_id;
			Messages::addMessage('The item has been removed', 'success');
            $return = true;
        } else {
            Messages::addMessage('You do not have permission to edit this item.', 'error');
            $return = false;
        }
		
        return $return;
    }
    
    
    /**
     * Create a backup file for the syllabus
     * @return mixed Returns the generated XML document on success, false otherwise
     */
    public function backupSyllabus() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            $S = new SyllabusModel();
            $S->getSyllabusById($this->syllabus_id);
            $syllabus = $S->result_array[0];
            $modules = $S->getModulesForSyllabus($this->syllabus_id);
            
            foreach($modules as $k => $v) {
                $syllabus['modules'][$v['module_type']]['module_custom_name'] = $v['module_custom_name']; 
                
                $method = 'get' . ucwords($v['module_type']) . 'ForSyllabus';
                $items = $S->{$method}($this->syllabus_id);
				if(is_array($items)) {
					foreach($items as $key => $item) {
						$syllabus['modules'][$v['module_type']]['items'][] = $item; 
					}
				}
            }
			
			$class_number = (isset($syllabus['syllabus_class_number'])) ? $syllabus['syllabus_class_number'] : 'DRAFT';
			
            $filename = $class_number . '_Backup_' . date('m-d-Y') . '.bak';
            $filename = str_replace(' ', '_', $filename);
            
            header("Content-disposition: attachment; filename=$filename");
            header("Content-type: text/plain");
            echo(json_encode($syllabus));
            exit;
        } else {
            Messages::addMessage('You do not have permission to make a backup of this syllabus.', 'error');
            return false;
        }
    }
    
    
    /**
     * Restore a syllabus from a file
     * @return bool Returns true on success, false otherwise
     */
    public function restoreSyllabus() {
        if($this->Permissions->canEditSyllabus($this->syllabus_id)) {
            if($_FILES['backup_file']['error'] == 0) {
                $uploaded_file = $_FILES['backup_file']['tmp_name'];
                $data = file_get_contents($uploaded_file);
                $syllabus = json_decode($data, true);
                
                if(is_array($syllabus)) {
                    if(count($this->restore_modules)) {
                        if(in_array('general', $this->restore_modules)) {
                            $this->query = sprintf(
                                "UPDATE syllabus SET syllabus_instructor='%s', syllabus_office='%s', syllabus_office_hours='%s', syllabus_phone='%s', syllabus_mobile='%s', syllabus_email='%s', syllabus_website='%s', syllabus_fax='%s', syllabus_class_description='%s' WHERE syllabus_id='%s';",
                                $syllabus['syllabus_instructor'],
                                $syllabus['syllabus_office'],
                                $syllabus['syllabus_office_hours'],
                                $syllabus['syllabus_phone'],
                                $syllabus['syllabus_mobile'],
                                $syllabus['syllabus_email'],
                                $syllabus['syllabus_website'],
                                $syllabus['syllabus_fax'],
                                $syllabus['syllabus_class_description'],
                                $this->syllabus_id
                            );
							
                            $this->executeQuery();
                        }
                        
                        if($this->restore_method == 'delete') {
                            $this->query = sprintf("DELETE FROM syllabus_modules WHERE syllabus_id='%s';", $this->syllabus_id);
                            $this->executeQuery();
                        }
                        
                        $module_order = 1;
                        foreach($syllabus['modules'] as $module_type => $module) {
                            if(in_array($module_type, $this->restore_modules)) {
                                $this->query = sprintf(
                                    "INSERT INTO syllabus_modules (syllabus_id, module_type, module_custom_name, module_order) VALUES ('%s', '%s', '%s', %d)
                                    ON DUPLICATE KEY UPDATE module_order=%d;",
                                    $this->syllabus_id,
                                    $module_type,
                                    $module['module_custom_name'],
                                    $module_order,
                                    $module_order
                                );
                                $this->executeQuery();
                                $module_order++;
                                
                                if($this->restore_method == 'delete') {
                                    $this->query = sprintf("DELETE FROM %s WHERE syllabus_id='%s';", $this->all_modules[$module_type]['db_name'], $this->syllabus_id);
                                    $this->executeQuery();
                                }
                                
                                if(isset($module['items']) && count($module['items'])) {
                                    foreach($module['items'] as $item_order => $item) {
                                        $this->query = sprintf("INSERT INTO %s SET syllabus_id='%s', ", $this->all_modules[$module_type]['db_name'], $this->syllabus_id);
                                        foreach($item as $k => $v) {
                                            $id_field = $this->all_modules[$module_type]['db_prefix'] . '_id';
                                            if($k != 'syllabus_id' && $k != $id_field) {
												$this->$k = $v; // assign to the model to trigger validation 
                                                $this->query .= sprintf(" %s='%s',", $k, $this->$k);
                                            }
                                        }
                                        $this->query = rtrim($this->query, ',');
                                        $this->query .= ';';
                                        $this->executeQuery();
                                    }
                                }
                            }
                        }
						
                        Messages::addMessage('The selected items were successfully restored.', 'success');
						$this->redirect = 'syllabus/backup_restore/' . $this->syllabus_id;
                        $return = true;
                        
                    } else {
                        Messages::addMessage('You must select at least one section to restore from the list.', 'error');
                        $return = false;
                    }
                } else {
                    Messages::addMessage('The backup file is not a valid backup file or is corrupt.  Please select a different backup file and try again.', 'error');
                    $return = false;
                }
            } else {
                switch($_FILES['backup_file']['error']) {
                    case 1: Messages::addMessage('The file size of the selected file exceeds the size allowed by the system. Please <a href="contact">contact the Syllabus development team</a> to report this problem.', 'error'); break;
                    case 2: Messages::addMessage('The file size of the selected file exceeds the size allowed by the system. Please <a href="contact">contact the Syllabus development team</a> to report this problem.', 'error'); break;
                    case 3: Messages::addMessage('File upload did not complete successfully. Please try again.', 'error'); break;
                    case 4: Messages::addMessage('No file was uploaded. Please select a backup file and submit the form again.', 'error'); break;
                    default: Messages::addMessage('An unknown error occurred.  Please try uploading your file again, or <a href="contact">contact the Syllabus development team</a> if the problem persists.', 'error'); break;
                }
                $return = false;
            }            
        } else {
            Messages::addMessage('You do not have permission to restore this syllabus.', 'error');
            $return = false;
        }
		
		return $return;
    }
	

}
