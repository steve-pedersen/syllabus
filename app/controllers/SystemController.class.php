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
		// override authentication on these methods
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