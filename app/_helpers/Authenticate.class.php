<?php

/**
 * Authentication class checks to see if the user is authenticated.  If not, and an authentication attempt was made,
 * it will attempt to log the user in.
 */
class Authenticate {
	
	/**
	 * @var object User Model
	 */
	private static $User;
	
	/**
	 * @var bool Flag for logged in status of user
	 */
	private static $is_logged_in;

    
    /**
     * Authenticate function.  If the user submitted the login form, attempt login. Check for a valid session.
     * @return bool Returns true if the user is authenticated, false otherwise
     */
	public static function isAuthenticated() {		
        if(isset($_SESSION['user_id'])) {
            return true;
        } else {
			self::logout();
			/*
			*/
            return false;
		}
	}

	
    /**
     * Local login function.  Attempt to log a user in with the submitted credentials
     */
	public static function login() {
		
		$id = $_POST['login_id'];
		self::$User = new UsersModel;
		
		if(false !== ($user = self::$User->getUserById($id))) {
			self::setSession($user);
		} else {
			echo('user not loaded');
			exit;
		}
    }
	

	
	/**
	 * Set the session data
	 * @param array $user The user object array
	 */
	public function setSession($user) {
		$_SESSION['user_id'] = $user['user_id'];
		$_SESSION['user_fname'] = $user['user_fname'];
		$_SESSION['user_lname'] = $user['user_lname'];
	}
	
	
    /**
     * Logout function
     */
	public function logout() {
		unset($_SESSION['user_id']);
		unset($_SESSION['user_fname']);
		unset($_SESSION['user_lname']);
		unset($_SESSION['user_email']);
		unset($_SESSION['user_perms']);
	}


}