<?php

/**
 * Set constants for permission strings so that even if the strings are changed, the code can remain consistent
 */
define('PERM_ADMIN', 'admin');

define('PERM_USERS_EDIT', 'edit_users');
define('PERM_USERS_PERMS', 'edit_permissions');
define('PERM_USERS_GHOST', 'ghost_users');
define('PERM_REPOSITORY', 'edit_repository');
define('PERM_BLOG', 'blog');
define('PERM_DRAFTS', 'drafts');



/**
 * Permissions Class. This class is only utilized upon login of authenticated users.  With login, permissions are
 * set to the user's session and that session array is used in future permission checks.
 */
class Permissions {
 
    /**
     * @var array Array of permissions for the current user.  The current user does not necessarily equate to the authenticated user.
     * For example, this array will be populated with another user's information when an administrator is setting permissions
     * for a user.  The array is populated via the buildUserPermissions() method.
     */
    private $permissions_array = array();
    
    
    /**
     * Build the permissions array for the provided user.
     * @param varchar $user_id The user's id
     */
    public function buildUserPermissions($user_id) {
        $U = new UsersModel;
        if(false !== ($result = $U->getUserPermissions($user_id))) {
            foreach($result as $k => $v) {
                if(isset($v['syllabus_id'])) {
                    $this->permissions_array['syllabus'][$v['syllabus_id']][$v['permission']] = true;
                } else {
                    $this->permissions_array[$v['permission']] = true;
                }
            }
        }
        
        if ($this->isInstructor()) {
            $this->permissions_array['drafts'] = true;
        }
    }
    
    
    /**
     * Get the permissions array
     * @return array The permissions array
     */
    public function getPermissionsArray() {
        return $this->permissions_array;
    }
    
    
    /**
     * Sets the current permissions array to the current session.
     */
    public function setPermissionsToSession() {
        $_SESSION['user_perms'] = $this->permissions_array;
    }
    
    
    /**
     * Load the permissions array from the current session.  
     */
    public function getPermissionsFromSession() {
        if(isset($_SESSION['user_perms'])) {
            $this->permissions_array = $_SESSION['user_perms'];
        }
    }
    
    
    
    
    
    
// =====================================================================================
// Permission check methods
// All of the methods below check permissions on the user associated with the calling instance of the Permissions class
// i.e. If you want to check permissions for a different user, you must instantiate a new object and buildUserPermissions() for that user

    /**
     * Checks if the user has administrative permission
     * @return bool Returns true if the user is an administrator, false otherwise
     */
    public function isAdmin() {
        return (isset($this->permissions_array[PERM_ADMIN])) ? true : false;
    }
    
    
    /**
     * If a syllabus_id is passed in, checks to see if the user is the instructor for the course
     * If no syllabus_id is passed in, checks to see if the user is the instructor for any course(s)
     * @param string $syllabus_id Optional syllabus id
     * @return bool Returns true if user is instructor, false otherwise
     */
    public function isInstructor($syllabus_id = NULL) {
        $S = new SyllabusModel();
        if(!is_null($syllabus_id)) {
            $instructor = $S->getSyllabusInstructor($syllabus_id);
            return ($instructor == $_SESSION['user_id']) ? true : false;
        } else {
            $courses = $S->getInstructorSyllabi();
            return (is_array($courses) && count($courses)) ? true : false;
        }
    }
    
    
    /**
     * Checks to see if the user has at least one administrative permission
     * @return bool Returns true if user has at least one administrative permission, false otherwise
     */
    public function hasAnyAdmin() {
        return (
            $this->isAdmin()
        );
    }
    
    
    /**
     * Generic permission check
     * @param string $perm The string name of the permission
     * @return bool Returns true if user has permission, false otherwise
     */
    public function hasPermission($perm) {
        return ($this->isAdmin() || isset($this->permissions_array[$perm])) ? true : false;
    }
    
    
    /**
     * Check to see if a user has permission to create / edit drafts
     * @return bool Return true if permision exists, false otherwise
    public function canCreateDrafts() {
        return ($this->isAdmin() || $this->isInstructor() || isset($this->permissions_array[PERM_DRAFTS]));
    }
     */
    
    
    /**
     * Check to see if the user can edit repository items
     * @return bool Returns true if user has permission to edit repository items, false otherwise
    public function canEditRepository() {
        return ($this->isAdmin() || isset($this->permissions_array[PERM_EDIT_REPOSITORY])) ? true : false;
    }
     */


    /**
     * Check to see if the user can view other users
     * @return bool Returns true if user has permission to edit users, false otherwise
     */
    public function canViewUsers() {
        return (
            $this->isAdmin() ||
            $this->hasPermission(PERM_USERS_EDIT) ||
            $this->hasPermission(PERM_USERS_PERMS) ||
            $this->hasPermission(PERM_USERS_GHOST) ||
            isset($this->permissions_array['view_users'])
        ) ? true : false;
    }


    /**
     * Check to see if the user can edit other users
     * @return bool Returns true if user has permission to edit users, false otherwise
    public function canEditUsers() {
        return ($this->isAdmin() || isset($this->permissions_array[PERM_USERS_EDIT])) ? true : false;
    }
     */
    
    
    /**
     * Check to see if the user can edit user permissions
     * @return bool Returns true if permission exists, false otherwise
    public function canEditPermissions() {
        return ($this->isAdmin() || isset($this->permissions_array[PERM_USERS_PERMS])) ? true : false;    
    }
     */
    
    
    /**
     * Check to see if the user can set other users as avatars
     * @return bool Returns true if permission exists, false otherwise
    public function canGhostUsers() {
        return ($this->isAdmin() || isset($this->permissions_array[PERM_USERS_PERMS])) ? true : false;
    }
     */
    
    
    /**
     * Check to see if the user has permission to manage blog postings
     * @return bool Returns true if permission exists, false otherwise
    public function canEditBlog() {
        return ($this->isAdmin() || isset($this->permissions_array[PERM_BLOG])) ? true : false;
    }
     */
    
    
    /**
     * Checks to see if the user has edit permission for a given syllabus
     * @param string $syllabus_id Unique id of the class that the syllabus belongs to
     * @return bool True if the user has permission to edit, false otherwise
     */
    public function canEditSyllabus($syllabus_id) {
        $return = false;
        $S = new SyllabusModel();
        
        if($this->isAdmin()) {
            $return = true;
        }
        
        if(isset($_SESSION['user_id']) && $S->getSyllabusInstructor($syllabus_id) == $_SESSION['user_id']) {
            $return = true;
        }
        
        if(false != ($syllabus = $S->getSyllabusById($syllabus_id))) {
            if(isset($_SESSION['user_id']) && $syllabus['syllabus_draft_owner'] == $_SESSION['user_id']) {
                $return = true;
            }
        }
        
        if(isset($this->permissions_array['syllabus'][$syllabus_id]['edit_syllabus'])) {
            $return = true;
        }
        
        return $return;
    }
    
    
    /**
     * Checks to see if the user has view permission for a given syllabus
     * @param string $syllabus_id Unique id of the class that the syllabus belongs to
     * @return bool True if user has view permission, false otherwise
     */
    public function canViewSyllabus($syllabus_id) {
        $S = new SyllabusModel();
        $syllabus = $S->getSyllabusById($syllabus_id);
        $syllabus_visibility = $syllabus['syllabus_visibility'];
        $syllabus_draft_owner = $syllabus['syllabus_draft_owner'];
        $perm_owner = 3;
        $perm_editor = 2;
        $perm_member = 1;
        $perm_public = 0;
        $perm_level = 0;
        
        switch(true) {
            case ($this->isAdmin()):                                                                $perm_level = $perm_owner;      break;
            case ($S->isClassInstructor($syllabus_id)):                                             $perm_level = $perm_owner;      break;
            case (isset($_SESSION['user_id']) && $syllabus_draft_owner == $_SESSION['user_id']):    $perm_level = $perm_owner;      break;
            case ($this->canEditSyllabus($syllabus_id)):                                            $perm_level = $perm_editor;     break;
            case ($S->isClassStudent($syllabus_id)):                                                $perm_level = $perm_member;     break;
            case (isset($_SESSION['allow_temporary_view'][$syllabus_id])):                          $perm_level = $perm_editor;     break;
            default:                                                                                $perm_level = $perm_public;     break;
        }
        
        switch($syllabus_visibility) {
            case 'editors': $return = ($perm_level >= $perm_editor) ? true : false; break;
            case 'members': $return = ($perm_level >= $perm_member) ? true : false; break;
            case 'public': $return = true; break;
            default: $return = false; break;
        }
        
        return ($return) ? true : false;
    }
    
    
    /**
     * Checks to see if the user has permission to export the syllabus in another format
     * @param string $syllabus_id Unique id of the class that the syllabus belongs to
     * @return bool True if user has view permission, false otherwise
     */
    public function canExportSyllabus($syllabus_id) {
        if($this->canViewSyllabus($syllabus_id)) {
            $return = true;    
        } else {
            $return = false;
        }
        
        return $return;
    }

}