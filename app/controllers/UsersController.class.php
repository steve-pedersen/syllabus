<?php

/**
 * Users Controller
 */
class UsersController extends BaseController {


    /**
     * The optional setup() method can be used to handle any child-specific setup that needs to take place before the more
     * generic setup in the BaseController.  For example, overriding the authentication of certain methods must be done here
     */
	protected function setup() {
		// override authentication on these methods
	}
	
	
	/**
	 * Load a user
	 * @return mixed Returns the result array on success, boolean false on failure
	 */
	private function load() {
		if(isset($this->url_vars[2])) {
			$user = $this->Model->getUserById($this->url_vars[2]);
		}
		
		if(isset($user) && is_array($user)) {
			$user = $this->mergePost($user);
			return $user;
		} else {
			Messages::addMessage('Invalid user. Please return to the <a href="users">Users page</a> and select a user.', 'error');
			return false;
		}
	}
	
    
    /**
     * List users
     */
    protected function index() {
		if($this->Permissions->canViewUsers()) {
			$this->View->addAdminLink('Users');
			$this->View->page_title = 'Users';
			if(isset($_GET['search']) && !empty($_GET['search'])) {
				$this->View->search = $_GET['search'];
				$users = $this->Model->searchUsers($_GET['search']);
				switch(true) {
					case (count($users) == 0):
						$this->View->search_message = '<div class="message error">Your search did not return any results.</div>';
						break;
					
					case (count($users) > 100):
						$this->View->search_message = '<div class="message error">Your search returned over 100 results.  Please narrow your search and try again.</div>';
						break;
					
					default:
						$this->View->users = $users;
						break;
				}
			} else {
				$this->View->search_message = '<div class="message info">Use the form above to search for users by name, email address or user id.</div>';
			}
			
			$this->View->parseTemplate('page_content', 'users/index.tpl.php');
		} else {
			Messages::addMessage('You do not have permission to manage users.', 'error');
		}
    }
	
	
	/**
	 * View a user
	 */
	protected function view() {
		if($this->Permissions->canViewUsers() || $this->url_vars[2] == $_SESSION['user_id']) {
			$this->View->addAdminLink('Users', 'users');
			$this->View->page_title = 'View User'; 
			if(false !== ($user = $this->load())) {			
				$user['user_phone'] = Utility::formatPhoneNumber($user['user_phone'], 'print');
				$user['user_mobile'] = Utility::formatPhoneNumber($user['user_mobile'], 'print');
				$user['user_fax'] = Utility::formatPhoneNumber($user['user_fax'], 'print');
				
				$this->View->addAdminLink('View');
				$this->View->enable_edit = ($this->Permissions->hasPermission(PERM_USERS_EDIT) || $user['user_id'] == $_SESSION['user_id']) ? true : false;
				$this->View->enable_perms = ($this->Permissions->hasPermission(PERM_USERS_PERMS)) ? true : false;
				$this->View->enable_ghost = ($this->Permissions->hasPermission(PERM_USERS_GHOST)) ? true : false;
				$this->View->user = $user;
				$this->View->parseTemplate('page_content', 'users/view.tpl.php');
			}
		} else {
			Messages::addMessage('You do not have permission to view this user account.', 'error');
		}
	}
	
	
	/**
	 * Edit a user
	 */
	protected function edit() {
		if($this->Permissions->hasPermission(PERM_USERS_EDIT) || $this->url_vars[2] == $_SESSION['user_id']) {
			if($this->Permissions->hasPermission(PERM_USERS_EDIT)) {
				$this->View->addAdminLink('Users', 'users');
				$this->View->addAdminLink('Edit');
			}
			$this->View->page_title = 'Edit User';
			if(false !== ($user = $this->load())) {
				$user['user_phone'] = Utility::formatPhoneNumber($user['user_phone'], 'print');
				$user['user_mobile'] = Utility::formatPhoneNumber($user['user_mobile'], 'print');
				$user['user_fax'] = Utility::formatPhoneNumber($user['user_fax'], 'print');
				$this->View->user = $user;
				$this->View->parseTemplate('page_content', 'users/edit.tpl.php');
			}
		} else {
			Messages::addMessage('You do not have permission to edit this user account.', 'error');
		}
	}
	
	
	/**
	 * User permissions
	 */
	protected function permissions() {
		if($this->Permissions->hasPermission(PERM_USERS_PERMS)) {
			if($this->Permissions->hasPermission(PERM_USERS_EDIT)) {
				$this->View->addAdminLink('Users', 'users');
				$this->View->addAdminLink('Permissions');
			}
			$this->View->page_title = 'Edit User Permissions'; 
			if(false !== ($user = $this->load())) {
				$this->View->page_title = 'Edit User Permissions';
				
				$P = new Permissions;
				$P->buildUserPermissions($user['user_id']);
				$perms = $P->getPermissionsArray();
				$this->View->perms = $perms;
				$this->View->user = $user;
				$this->View->can_set_admin = $this->Permissions->isAdmin();
				
				$this->View->parseTemplate('page_content', 'users/permissions.tpl.php');
			}
		} else {
			Messages::addMessage('You do not have permission to edit user permissions.', 'error');
		}
	}
	
	
	
	/**
	 * Set Ghost
	 */
	protected function ghost() {
		if($this->Permissions->hasPermission(PERM_USERS_GHOST)) {
			if(false != ($ghost = $this->load())) {
				$this->unghost(false);
			   
				$revert = $_SESSION;
				$_SESSION['revert'] = $revert;
				$_SESSION['user_id'] = $ghost['user_id'];
				$_SESSION['user_fname'] = $ghost['user_fname'];
				$_SESSION['user_lname'] = $ghost['user_lname'];
				$_SESSION['in_ghost_mode'] = true;
				
				// only add the user's syllabus specific permissions.  All other permissions should not transfer
				// this will prevent using ghosting to access higher permission such as admin
				$P = new Permissions;
				$P->buildUserPermissions($ghost['user_id']);
				$ghost_perms = $P->getPermissionsArray();
				if(isset($ghost_perms['syllabus'])) {
					$_SESSION['user_perms']['syllabus'] = $ghost_perms['syllabus'];
				}
				Messages::addMessage('Ghost set', 'success');
				Utility::redirect('users');
			}
		} else {
			Messages::addMessage('You do not have permission to ghost other users.', 'error');
		}
	}
	
	
	/**
	 * Unset Ghost
	 */
	protected function unghost($redirect = true) {
		if(isset($_SESSION['in_ghost_mode']) && $_SESSION['in_ghost_mode'] == true) {
			unset($_SESSION['in_ghost_mode']);
		}
		
		if(isset($_SESSION['revert'])) {
            $revert = $_SESSION['revert'];
            $_SESSION = $revert;
            unset($_SESSION['in_ghost_mode']);
        }
		
		if($redirect) {
			Messages::addMessage('Ghost unset', 'success');
			Utility::redirect($_GET['return_url']);
		}
	}


    
}