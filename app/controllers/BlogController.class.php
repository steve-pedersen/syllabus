<?php

/**
 * Blog Controller
 */
class BlogController extends BaseController {


    /**
     * The optional setup() method can be used to handle any child-specific setup that needs to take place before the more
     * generic setup in the BaseController.  For example, overriding the authentication of certain methods must be done here
     */
	protected function setup() {
		// override authentication on these methods
		$this->disable_auth_array[] = 'index';
		$this->disable_auth_array[] = 'view';
		$this->disable_auth_array[] = 'feed';
	}
	
	
	/**
	 * Load a blog entry
	 * @return mixed Returns the result array on success, boolean false on failure
	 */
	private function load() {
		if(isset($this->url_vars[2])) {
			$post = $this->Model->getPostById($this->url_vars[2]);
		}
		
		if(isset($post) && is_array($post)) {
			$post = $this->mergePost($post);
			return $post;
		} else {
			Messages::addMessage('Invalid blog post.', 'error');
			return false;
		}
	}
	
    
    /**
     * List users
     */
    protected function index() {
		
        $this->View->page_title = 'Blog Archive';
        $this->View->posts = $this->Model->getPublishedPosts();
        $this->View->parseTemplate('page_sidebar', '_fragments/sidebar_blog.tpl.php');
        $this->View->parseTemplate('page_content', 'blog/index.tpl.php');
    }
    
    
    /**
     * View a blog post
     */
    protected function view() {
        if(false != ($post = $this->load())) {
            $this->View->page_title = 'Blog: ' . $post['post_title'];
            $this->View->post = $post;
            $this->View->parseTemplate('page_sidebar', '_fragments/sidebar_blog.tpl.php');
            $this->View->parseTemplate('page_content', 'blog/view.tpl.php');
        }
    }


    /**
     * Manage blog posts
     */
    protected function manage() {
        if($this->Permissions->hasPermission(PERM_BLOG)) {
            $this->View->addAdminLink('Blog');
            $this->View->page_title = 'Manage Blog';
            $this->View->posts = $this->Model->getPosts();
            $this->View->parseTemplate('page_content', 'blog/manage.tpl.php');
        } else {
            $this->View->error_message = 'You do not have permission to edit the blog.';
            $this->View->parseTemplate('page_content', 'index/error.tpl.php');
        }
    }


    /**
     * Create a blog post
     */
    protected function create() {
        if($this->Permissions->hasPermission(PERM_BLOG)) {
            $this->View->addAdminLink('Blog', 'blog/manage');
            $this->View->addAdminLink('Create Post');
            $this->View->page_title = 'Create Blog Post';
			$this->View->page_header = 'Create a new blog post';
			$this->View->command = 'createPost';
			$this->View->parseTemplate('page_content', 'blog/form.tpl.php');
        } else {
			Messages::addMessage('You do not have permission to create blog posts.', 'error');
        }
    }


    /**
     * Edit a blog post
     */
    protected function edit() {
        if($this->Permissions->hasPermission(PERM_BLOG)) {
            $this->View->addAdminLink('Blog', 'blog/manage');
            $this->View->addAdminLink('Edit Post');
            $this->View->page_title = 'Edit Blog Post';
            $this->View->page_header = 'Edit post';
            if(false != ($post = $this->load())) {
                $this->View->command = 'editPost';
                $this->View->post = $post;
                $this->View->parseTemplate('page_content', 'blog/form.tpl.php');
			}
        } else {
			Messages::addMessage('You do not have permission to manage the blog.', 'error');
        }
    }
    
    
    /**
     * Build the RSS feed
     */
    protected function feed() {
        $this->View->posts = $this->Model->getPublishedPosts();
        $this->View->pub_date = date('r',time());
        $this->View->setView('feed');
		
		// output headers
		header("Content-Type: application/xml; charset=ISO-8859-1"); 
    }

    
}