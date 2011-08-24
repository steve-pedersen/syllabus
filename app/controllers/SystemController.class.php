<?php

/**
 * Index Controller
 */
class SystemController extends BaseController {


    /**
     * The optional setup() method can be used to handle any child-specific setup that needs to take place before the more
     * generic setup in the BaseController.  For example, overriding the authentication of certain methods must be done here
     */
	protected function setup() {
		// allow access to the modify method .. access will be checked more granularly via that method
		$this->disable_auth_array[] = 'create_users';
	}
    
    
    /**
     * Index
     */
    protected function index() {
        $this->View->page_title = 'System Administration';
        $this->View->parseTemplate('page_content', 'system/index.tpl.php');
    }


	/**
	 * Stats page
	 */
	protected function stats() {
		if($this->Permissions->isAdmin()) {
			$this->View->addAdminLink('Statistics');
			$this->View->page_title = 'Statistics';
			// get number of active syllabi
			$S = new SyllabusModel;
			$this->View->syllabus_count = $S->getSyllabusCount();
			$this->View->active_syllabus_count = $S->getActiveSyllabusCount();
			
			$this->View->parseTemplate('page_content', 'system/stats.tpl.php');
		} else {
			
		}
		
	}
	
	
	/**
	 * System page
	 */
	protected function update() {
		if($this->Permissions->isAdmin()) {
			$this->View->page_title = 'System';
			$this->View->show_merge_legacy = false;
			$this->View->parseTemplate('page_content', 'system/update.tpl.php');
		} else {
			Messages::addMessage('You do not have permission to access this page.', 'error');
		}
	}
    
	
	/**
	 * Modify the database to make changes for testing
	 * This method will be publicly accessible (no authorization required) if DEBUG_MODE = true in the config file
	 * authorization turned off via the setup() method of this class
	 */
	public function create_users() {
		if(DEBUG_MODE) {
			$this->View->page_title = "Create Testing Users";
			$this->View->parseTemplate('page_content', 'system/create_users.tpl.php');
		} else {
			Messages::addMessage('Users can only be created for sites that are in debug mode.', 'error');
		}
	}
	
	
	/**
	 * Assign a user a specific role for a specific course
	 */
	public function assign() {
		if($this->Permissions->isAdmin()) {
			$this->View->page_title = "Assign User to Course";
			$this->View->parseTemplate('page_content', 'system/assign.tpl.php');
		} else {
			Messages::addMessage('You must be an administrator to access this page', 'error');
		}
	}
	
	/**
	 * PHP Info
	 */
	protected function phpinfo() {
        if($this->Permissions->isAdmin()) {
            phpinfo();
            exit;
        } else {
            $this->index();
        }
	}
    
}