<?php

/**
 * The BaseController class is the parent from which all other controllers extend.  The BaseController serves as a front controller
 * and sets up all of the necessary classes for the processing of the page (e.g. instantiating the appropriate Model, handling authentication,
 * catching and processing form submissions via the appropriate model, etc).
 */


class BaseController {

    /**
     * @var array Array of variables from the parsed URL
     */
    public $url_vars = array();

    /**
     * @var array Array of methods for which authentication is not necessary. Child methods should be added in the child controller's init() method
     */
    protected $disable_auth_array = array('shibboleth', 'logout', 'maintenance');
    
    /**
     * @var string Name of the method that will be called by the child controller (based on URL)
     */
    private $method_name;
    
    /**
     * @var object The model associated with this controller.
     */
    protected $Model;
    
    /**
     * @var object The view associated with this controller.
     */
    protected $View;
    
    /**
     * @var object The permissions object
     */
    protected $Permissions;
    


    /**
     * Constructor method. The constructor does the basic setup of the framework including requesting child-controller-specific
     * setup via the setup() method and instantiating any necessary classes for the continuation of the processing
     */
    public function __construct() {
        // if there are messages in the SESSION, merge them and unset them from the session
        if(isset($_SESSION['messages'])) {
            Messages::mergeMessages($_SESSION['messages']);
            unset($_SESSION['messages']);
        }
        
        // instantiate classes
        $this->Permissions = new Permissions();
        $model_name = str_replace('Controller', 'Model', get_class($this));
        $this->Model = new $model_name($this->Permissions);
        
        $this->View = new Template();
        if(isset($_GET['view'])) {
            $this->View->setView($_GET['view']);
        }
        
        // do pre-emptory setup in the child controller
        if(method_exists($this, 'setup')) {
            $this->setup();
        }
    }
    
    
    /**
     * Init method.  The init method is the main method of the BaseController which runs all the necessary pre-processing
     * functions, handles submission, calls the appropriate child class method to build the view, and displays the view
     */
    public function init() {
        // Set the method to run
        $method_key = (get_class($this) == 'IndexController') ? 0 : 1;
        $this->method_name = (isset($this->url_vars[$method_key]) && method_exists($this, $this->url_vars[$method_key]))
            ? $this->url_vars[$method_key]
            : 'index';
        
        // Authenticate
        $this->authenticate();
        
        // Handle any submissions
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handleSubmission();
        }
        
        // Call the child method
        $this->{$this->method_name}();
        
        // Render the view
        $this->View->messages = Messages::getMessagesHtml();
        $this->View->render();
    }       
    
    
    /**
     * Authentication wrapper
     */
    private function authenticate() {
        if(defined('SUBMIT_METHOD') && SUBMIT_METHOD == 'login') {
            Authenticate::login();
        }
        
        if(!in_array($this->method_name, $this->disable_auth_array)) {
            Authenticate::isAuthenticated();
        }
        
        $this->buildUserPermissions();
    }
    
    
    /**
     * Build the user's permissions
     */
    private function buildUserPermissions() {
        if(isset($_SESSION['user_perms'])) {
            $this->Permissions->getPermissionsFromSession();
        } elseif(isset($_SESSION['user_id'])) {
            $this->Permissions->buildUserPermissions($_SESSION['user_id']);
            $this->Permissions->setPermissionsToSession();
        }
        
        // if the user has an admin permission, add the root item to the admin navigation so that it appears
        if($this->Permissions->hasAnyAdmin()) {
            $this->View->addAdminLink('Administration', 'admin');
        }
    }
    
    
    /**
     * The submit method handles any form submissions and calls the appropriate model methods to process the data
     */
    private function handleSubmission() {
        // check the submit token for validity
        if(SUBMIT_TOKEN_POSTED == SUBMIT_TOKEN) {
            $valid = true;
            
            foreach($_POST as $k => $v) {
                // attempt to set the data to the Model. This will trigger the BaseModel __set() method
                $this->Model->$k = $v;
                if(!isset($this->Model->$k)) {
                    $valid = false;
                }
            }
            
            if($valid) {            
                if(method_exists($this->Model, SUBMIT_METHOD) && $this->Model->{SUBMIT_METHOD}()) {                    
                    if(isset($this->Model->redirect) && !isset($this->Model->ajax_submit)) {
                        $_SESSION['messages'] = Messages::getMessagesArray();
                        Utility::redirect($this->Model->redirect);
                    } elseif(isset($this->Model->ajax_submit) && $this->Model->ajax_submit == true) {
                        $Ajax = new AjaxResponse($this->Model, $this->View);
                        if(method_exists($Ajax, SUBMIT_METHOD)) {
                            $Ajax->{SUBMIT_METHOD}();
                            $Ajax->printResponse();
                            exit;
                        }
                    }
                } else {
                    if(isset($this->Model->ajax_submit) && $this->Model->ajax_submit == true) {
                        $Ajax = new AjaxResponse($this->Model, $this->View);
                        $Ajax->formSubmissionError();
                        $Ajax->printResponse();
                        exit;
                    }
                }
            }
        } else {
            Messages::addMessage('Submissions originating from other sites are prohibited', 'error');
        }
    }
    
    
    /**
     * Merge any posted data into an array, overwriting the original values with the $_POST values.  
     * @param array $target_array The target array to merge the $_POST data into
     * @return array Returns the merged array
     */
    protected function mergePost($target_array = array()) {
        return (is_array($target_array) && is_array($_POST))
            ? array_merge($target_array, $_POST)
            : array();
    }
    
    
    /**
     * Login
     */
    protected function login() {
    }
    
    
    /**
     * Logout
     */
    protected function logout() {
        Authenticate::logout();
    }
	
	
	/**
	 * Shibboleth landing page. This is the landing page for all Shibboleth requests.  This page parses the shibboleth return headers
	 * and redirects to the appropriate page
	 */
	protected function shibboleth() {
		if(false != Authenticate::login()) {
			$this->Permissions->buildUserPermissions($_SERVER['HTTP_UID']);
			$this->Permissions->setPermissionsToSession();
			Utility::redirect($_GET['redirect']);
		} else {
			$this->View->page_title = 'Login Error';
			$this->View->addNavLink('Login Error');
		}
	}
	
	
	/**
	 * Login to the sandbox account
	 */
	protected function sandbox() {
		if(SANDBOX_ACCT_ENABLE) {
			if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != SANDBOX_ACCT_USER) {
				$this->View->error_message = 'You are already logged in as ' . $_SESSION['user_fname'] . ' ' . $_SESSION['user_lname'] .'. You must logout to use the sandbox account.';
				$this->View->parseTemplate('page_content', 'index/error.tpl.php');			
			} else {
				$_SESSION['user_id'] = 'sandbox';
				$_SESSION['user_fname'] = 'Sandbox';
				$_SESSION['user_lname'] = 'User';
				$this->Permissions->buildUserPermissions(SANDBOX_ACCT_USER);
				$this->Permissions->setPermissionsToSession();
				Utility::redirect('syllabus');
			}
		} else {
            $this->View->error_message = 'The sandbox account is not available on this server.';
            $this->View->parseTemplate('page_content', 'index/error.tpl.php');			
		}
	}
	
	
	/**
	 * output the mainenance page
	 */
	protected function maintenance() {
		if(MAINTENANCE_MODE) {
			$this->View->page_title = "Scheduled Maintenance";
			$this->View->maintenance_endtime = date('l M j, Y, g:i a T', strtotime(MAINTENANCE_ENDTIME));
			$this->View->parseTemplate('page_content', 'index/maintenance.tpl.php');
		} else {
			$this->index();
		}
	}

    
}