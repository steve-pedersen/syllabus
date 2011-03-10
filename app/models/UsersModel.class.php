<?php

/**
 * Users Model
 */
class UsersModel extends BaseModel {	


    /**
     * Get all users
     * @return array Returns the query result array
     */
    public function getUsers() {
        $this->query = sprintf("SELECT * FROM users ORDER BY user_lname, user_fname ASC;");
        $result = $this->executeQuery();
        return ($result['count'] > 0) ? $result['data'] : false;
    }

    
    /**
     * Get a specific user by id
     * @param int $user_id The user's id
     * @return array Returns the query result as an array
     */
    public function getUserById($user_id) {
        Validate::user_id($user_id);
        $this->query = sprintf("SELECT * FROM users WHERE user_id='%s';", $user_id);
        $result = $this->executeQuery();
        
        if($result['count'] == 1) {
            if(empty($result['data'][0]['user_preferred_name'])) {
                $result['data'][0]['user_preferred_name'] = $result['data'][0]['user_fname'] . ' ' . $result['data'][0]['user_lname'];
            }
        }
        
        return ($result['count'] == 1) ? $result['data'][0] : false;
    }
    
    
    /**
     * Get a specific user by email
     * @param string $email The user's email address
     * @return array Returns the query result as an array, false on fail
     */
    public function getUserByEmail($email) {
        Validate::user_email($email);
        $this->query = sprintf("SELECT * FROM users WHERE user_email='%s';", $email);
        $result = $this->executeQuery();
        return ($result['count'] == 1) ? $result['data'][0] : false;
    }
    
    
    /**
     * Search Users
     * @param string $search The search string
     * @return array Returns and array of all matches or false if no matches
     */
    public function searchUsers($search) {
        $this->s = $search;
        $search_terms = explode(' ', $this->s);
        
        $query = "SELECT * FROM users WHERE 1=1 ";
        $params = array();
        
        foreach($search_terms as $k => $v) {
            $query .= " AND (user_id='%s' OR user_fname LIKE %s OR user_lname LIKE %s OR user_email LIKE %s) ";
            $params[] = $v;
            $params[] =  "'%" . $v . "%'";
            $params[] =  "'%" . $v . "%'";
            $params[] =  "'%" . $v . "%'";
        }
        
        $query .= " ORDER BY user_lname, user_fname ASC LIMIT 101";
        
        $this->query = vsprintf($query, $params);
        $result = $this->executeQuery();
        
        return ($result['count'] > 0) ? $result['data'] : array();
    }
    
    
    /**
     * Get the permissions for a user
     * @param string $user_id The user's id
     * @return array Returns the permissions array
     */
    public function getUserPermissions($user_id) {
        Validate::user_id($user_id);
        $this->query = sprintf("SELECT * FROM permissions WHERE user_id='%s';", $user_id);
        $result = $this->executeQuery();
        
        return ($result['count'] > 0) ? $result['data'] : array();
    }
    
    
    /**
     * Edit user
     * @return bool Return true on success, false on fail
     */
    public function editUser() {
        if($this->Permissions->hasPermission(PERM_USERS_EDIT) || $this->user_id == $_SESSION['user_id']) {
            $this->query = sprintf(
                "UPDATE users SET user_preferred_name='%s', user_email='%s', user_office='%s', user_phone='%s', user_mobile='%s', user_fax='%s' WHERE user_id='%s';",
                $this->user_preferred_name,
                $this->user_email,
                $this->user_office,
                Utility::formatPhoneNumber($this->user_phone),
                Utility::formatPhoneNumber($this->user_mobile),
                Utility::formatPhoneNumber($this->user_fax),
                $this->user_id
            );
            $this->executeQuery();
            Messages::addMessage('User was edited successfully', 'success');
            $this->redirect = 'users/view/' . $this->user_id;
            $return = true;
        } else {
            Messages::addMessage('You do not have permission to edit this user.', 'error');
            $return = false;
        }
        
        return $return;
    }
    
    /**
     * Edit user permissions
     * @return bool Returns true if successful, false otherwise
     */
    public function editUserPermissions() {
        if($this->Permissions->hasPermission(PERM_USERS_PERMS)) {
            // set the admin priveledge only if the current user is also an admin
            $this->query = ($this->Permissions->isAdmin())
                ? sprintf("DELETE FROM permissions WHERE user_id='%s';", $this->user_id)
                : sprintf("DELETE FROM permissions WHERE user_id='%s' AND permission!='admin';", $this->user_id);
            $this->executeQuery();
            
            // set all other permissions
            if(isset($this->permissions) && is_array($this->permissions)) {
                foreach($this->permissions as $k => $v) {
                    if($k != PERM_ADMIN || $this->Permissions->isAdmin()) {
                        $this->query = sprintf("INSERT INTO permissions SET user_id='%s', permission='%s';", $this->user_id, $v);
                        $this->executeQuery();
                    }                
                }
            }
            
            $this->redirect = 'users/view/' . $this->user_id;
            Messages::addMessage('The user\'s permissions were changed successfully', 'success');
            $return = true;
        } else {
            Messages::addMessage('You do not have permission to set user permissions.', 'error');
            $return = false;
        }
        
        return $return;
    }



}