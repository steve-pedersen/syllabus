<?php

/**
 * Blog Model
 */
class BlogModel extends BaseModel {	


    /**
     * Get all the blog posts
     * @param int $limit Limit the amount of posts to be returned
     * @return array Return the result array
     */
    public function getPosts() {
        $this->query = "SELECT * FROM blog b INNER JOIN users u ON b.post_author=u.user_id ORDER BY post_publish_date DESC";
        $result = $this->executeQuery();
        return ($result['count'] > 0) ? $result['data'] : array();
    }
    
    
    /**
     * Customized query to retrieve posts to be displayed on the homepage
     * @return array Returns the result array
     */
    public function getPublishedPosts($options = array()) {
        $query = "SELECT * FROM blog b WHERE b.post_publish_date<" . time();
        $values = array();
        
        if(!isset($options['show_archived']) || $options['show_archived'] == false) {
            $query .= " AND b.post_archived=0";
        }
        
        $query .= " ORDER BY post_sticky DESC, post_publish_date DESC ";
        
        if(isset($options['limit']) && is_numeric($options['limit'])) {
            $query .= " LIMIT %s";
            $values[] = $options['limit'];
        }
        
        $this->query = vsprintf($query, $values);
        $result = $this->executeQuery();
        return ($result['count'] > 0) ? $result['data'] : array();
    }


    /**
     * Get a specific post by its id
     * @param int $id The blog post's id
     * @return array Returns the result array for the post, false if fail
     */
    public function getPostById($id) {
        $this->query = sprintf("SELECT * FROM blog b INNER JOIN users u ON b.post_author=u.user_id WHERE post_id=%d;", $id);
        $result = $this->executeQuery();
        return ($result['count'] == 1) ? $result['data'][0] : false;
    }


    /**
     * Add a blog posting to the database
     * @return bool Returns true if suceessful, false otherwise
     */
    public function createPost() {
        if($this->Permissions->hasPermission(PERM_BLOG)) {
            $this->query = sprintf(
                "INSERT INTO blog SET post_title='%s', post_text='%s', post_author='%s', post_publish_date='%s';",
                $this->post_title,
                $this->post_text,
                $_SESSION['user_id'],
                (!empty($this->post_publish_date))
                    ? strtotime($this->post_publish_date)
                    : strtotime(date('n/j/y', time()))
            );
            
            $this->executeQuery();
            Messages::addMessage('Blog posting successfully created.', 'success');
            $this->redirect = 'blog/manage';
            $return = true;
        } else {
            Messages::addMessage('You do not have permission to edit the blog.', 'error');
            $return = false;
        }
        
        return $return;
    }
    
    
    /**
     * Edit an existing post
     * @return bool Returns true on success, false otherwise
     */
    public function editPost() {
        if($this->Permissions->hasPermission(PERM_BLOG)) {
            $this->query = sprintf(
                "UPDATE blog SET post_title='%s', post_text='%s', post_publish_date='%s' WHERE post_id=%d;",
                $this->post_title,
                $this->post_text,
                (isset($this->post_publish_date))
                    ? strtotime($this->post_publish_date)
                    : strtotime(date('n/j/y', time())),
                $this->post_id
            );
            $this->executeQuery();
            Messages::addMessage('Blog posting successfully edited.', 'success');
            $this->redirect = 'blog/manage';
            $return = true;
        } else {
            Messages::addMessage('You do not have permission to edit the blog.', 'error');
            $return = false;
        }
        
        return $return;
    }
    
    
    /**
     * Change status for blog item(s)
     * @return bool Returns true if successful, false otherwise
     */
    public function changeStatus() {
        if($this->Permissions->hasPermission(PERM_BLOG)) {
            if(isset($this->posts) && is_array($this->posts) && count($this->posts)) {
                $method = 'do' . ucwords($this->bulk_action);
                if(isset($this->bulk_action) && method_exists($this, $method)) {
                    foreach($this->posts as $k => $v) {
                        $this->{$method}($v);
                    }
                    Messages::addMessage('The selected items have been successfully modified.', 'success');
                    $this->redirect = 'blog/manage';
                    $return = true;
                } else {
                    Messages::addMessage('You did not select an action to perform to the selected items.', 'error');
                    $return = false;
                }
            } else {
                Messages::addMessage('Please select at least one post to modify', 'error');
                $return = false;
            }
        } else {
            Messages::addMessage('You do not have permission to edit the blog.', 'error');
            $return = false;
        }
        return $return;
    }
    
    
    /**
     * Add sticky flag
     */
    private function doSticky($id) {
        $this->query = sprintf("UPDATE blog SET post_sticky=1 WHERE post_id=%d;", $id);
        $this->executeQuery();
    }
    
    
    /**
     * Remove sticky flag
     */
    private function doUnsticky($id) {
        $this->query = sprintf("UPDATE blog SET post_sticky=0 WHERE post_id=%d;", $id);
        $this->executeQuery();
    }
    
    
    /**
     * Add important flag
     */
    private function doImportant($id) {
        $this->query = sprintf("UPDATE blog SET post_important=1 WHERE post_id=%d;", $id);
        $this->executeQuery();
    }
    
    
    /**
     * Remove important flag
     */
    private function doUnimportant($id) {
        $this->query = sprintf("UPDATE blog SET post_important=0 WHERE post_id=%d;", $id);
        $this->executeQuery();
    }
    
    
    /**
     * Add archive flag
     */
    private function doArchive($id) {
        $this->query = sprintf("UPDATE blog SET post_archived=1 WHERE post_id=%d;", $id);
        $this->executeQuery();
    }
    
    
    /**
     * Remove archive flag
     */
    private function doUnarchive($id) {
        $this->query = sprintf("UPDATE blog SET post_archived=0 WHERE post_id=%d;", $id);
        $this->executeQuery();
    }


    /**
     * Delete the item
     */
    private function doDelete($id) {
        $this->query = sprintf("DELETE FROM blog WHERE post_id=%d;", $id);
        $this->executeQuery();
    }
    


}