<?php

/**
 * Index Controller
 */
class IndexController extends BaseController {


    /**
     * The optional setup() method can be used to handle any child-specific setup that needs to take place before the more
     * generic setup in the BaseController.  For example, overriding the authentication of certain methods must be done here
     */
	protected function setup() {
		// override authentication on these methods
		$this->disable_auth_array[] = 'index';
		$this->disable_auth_array[] = 'contact';
		$this->disable_auth_array[] = 'accessibility';
		$this->disable_auth_array[] = 'sandbox';
	}
    
    
    /**
     * Index
     */
    protected function index() {
        $B = new BlogModel();
        $posts = $B->getPublishedPosts(array('limit'=>5, 'show_archived'=>false));
        $this->View->posts = $posts;
        $this->View->page_title = 'Home';
        $this->View->parseTemplate('page_sidebar', '_fragments/sidebar_blog.tpl.php');
        $this->View->parseTemplate('page_content', 'index/index.tpl.php');
    }
	
    
    /**
     * Contact
     */
    protected function contact() {
        $this->View->page_title = 'Contact Us';
		$this->View->addNavLink('Contact Us');
		$this->View->parseTemplate('page_content', 'index/contact.tpl.php');
    }
	
	
	/**
	 * Accessibility
	 */
	protected function accessibility() {
		$this->View->page_title = 'Accessibility';
		$this->View->addNavLink('Accessibility');
		$this->View->parseTemplate('page_content', 'index/accessibility.tpl.php');
	}


	/**
	 * Admin home page
	 */
	protected function admin() {
        $this->View->addAdminLink('Dashboard');
        $this->View->page_title = 'Admin Dashboard';
        if($this->Permissions->hasAnyAdmin()) {
            $this->View->show_users_link = $this->Permissions->canViewUsers();
            $this->View->show_repository_link = $this->Permissions->hasPermission(PERM_REPOSITORY);
            $this->View->show_system_link = $this->Permissions->isAdmin();
            $this->View->show_blog_link = $this->Permissions->hasPermission(PERM_BLOG);
            $this->View->parseTemplate('page_content', 'index/admin.tpl.php');
        } else {
			Messages::addMessage('You do not have sufficient priveleges to access this page.', 'error');
        }
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
			
			$this->View->parseTemplate('page_content', 'index/stats.tpl.php');
		} else {
			
		}
		
	}
	
	
	/**
	 * System page
	 */
	protected function system() {
		if($this->Permissions->isAdmin()) {
			$this->View->page_title = 'System';
			$this->View->show_merge_legacy = false;
			$this->View->parseTemplate('page_content', 'system/index.tpl.php');
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