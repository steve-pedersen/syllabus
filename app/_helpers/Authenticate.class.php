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
			$redirect = (CURRENT_URL == 'login') ? 'syllabus' : CURRENT_URL;
			Utility::redirect(SHIB_DIR . '?redirect=' . urlencode($redirect));
		}
	}

	
    /**
     * Local login function.  Attempt to log a user in with the submitted credentials
     * @return Boolean Returns true if login succeeds, false if it fails
     */
	public static function login() {
		$U = new UsersModel();
		if(false !== ($user = $U->getUserById($_SERVER['HTTP_UID']))) {
			self::setSession($user);
			$return = true;
		} else {
			Messages::addMessage('Your user does not exist in the Syllabus system.  Please <a href="contact">Contact the Syllabus Team</a> for more information.', 'error');
			$return = false;
		}
		
		return $return;
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
		if(isset($_SESSION)) {
            session_destroy();
            unset($_SESSION);
        }
		
		// Logout of Shibboleth
		$url = SHIB_IDP . '/idp/Logout';
		Utility::redirect($url);
	}


}