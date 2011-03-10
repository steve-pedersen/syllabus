<?php

/**
 * Repository Controller
 */
class RepositoryController extends BaseController {


    /**
     * The optional setup() method can be used to handle any child-specific setup that needs to take place before the more
     * generic setup in the BaseController.  For example, overriding the authentication of certain methods must be done here
     */
	protected function setup() {
		// override authentication on these methods
	}
	
	
	/**
	 * Load a repository item
	 * @return mixed Returns the result array on success, boolean false on failure
	 */
	private function load() {
		if(isset($this->url_vars[2]) && isset($this->url_vars[3])) {
			$S = new SyllabusModel;
			$item = $S->getModuleItem($this->url_vars[2], $this->url_vars[3]);
		}
		
		if(isset($item) && is_array($item)) {
			$item = $this->mergePost($item);
			return $item;
		} else {
			Messages::addMessage('Invalid repository item.', 'error');
			return false;
		}
	}


    /**
     * Build the index page
     */
	protected function index() {
        if($this->Permissions->hasPermission(PERM_REPOSITORY)) {
            $this->View->page_title = 'Manage Repository Items';
            $this->View->addAdminLink('Repository');
            $repository = $this->Model->getRepositoryItems();
            $this->View->repository = $repository;
            $this->View->parseTemplate('page_content', 'repository/index.tpl.php');
        } else {
			Messages::addMessage('You do not have permission to access this page.', 'error');
        }
	}


    /**
     * Create a repository item
     */
    protected function create() {
        if($this->Permissions->hasPermission(PERM_REPOSITORY)) {
            if(array_key_exists($this->url_vars[2], $this->Model->all_modules)) {
                $module_id = $this->url_vars[2];
                $this->View->page_title = 'Create Repository Item: ' . $this->Model->all_modules[$module_id]['singular'];
                $this->View->page_header = 'Create Repository Item: <span class="sub-header">' . $this->Model->all_modules[$module_id]['singular'] . '</span>';
                $this->View->addAdminLink('Repository', 'repository');
                $this->View->addAdminLink('Create Repository Item');
                $this->View->command = 'createRepositoryItem';
				$this->View->cancel = 'repository';
                $this->View->module = $this->Model->all_modules[$module_id];
                $this->View->syllabus_id = 'repository';
                $this->View->parseTemplate('page_content', 'modules/' . $module_id . '/form.tpl.php');
            } else {
                Messages::addMessage('Invalid Module. Please go to the <a href="repository">Repository Browse Page</a> and try again', 'error');
            }
        } else {
			Messages::addMessage('You do not have permission to access this page.', 'error');
        }
    }


    /**
     * Edit a repository item
     */
    protected function edit() {
        if($this->Permissions->hasPermission(PERM_REPOSITORY)) {
            if(array_key_exists($this->url_vars[2], $this->Model->all_modules)) {
				if(false != ($item = $this->load())) {
					$module_id = $this->url_vars[2];
					$this->View->page_title = 'Edit Repository Item';
					$this->View->page_header = 'Edit Repository Item';
					$this->View->addAdminLink('Repository', 'repository');
					$this->View->addAdminLink('Edit Repository Item');
					$this->View->command = 'editRepositoryItem';
					$this->View->cancel = 'repository';
					$this->View->module = $this->Model->all_modules[$module_id];
					$this->View->syllabus_id = 'repository';
					$this->View->item = $item;
					$this->View->parseTemplate('page_content', 'modules/' . $module_id . '/form.tpl.php');
				}
            } else {
                Messages::addMessage('Invalid Module. Please go to the <a href="repository">Repository Browse Page</a> and try again', 'error');
            }
        } else {
			Messages::addMessage('You do not have permission to access this page.', 'error');
        }
    }


    /**
     * Delete a repository item
     */
    protected function delete() {
        if($this->Permissions->hasPermission(PERM_REPOSITORY)) {
            if(array_key_exists($this->url_vars[2], $this->Model->all_modules)) {
				if(false != ($item = $this->load())) {
					$module_id = $this->url_vars[2];
					$this->View->item_id = $this->url_vars[3];
					$this->View->page_title = 'Delete Repository Item';
					$this->View->page_header = 'Delete Repository Item';
					$this->View->addAdminLink('Repository', 'repository');
					$this->View->addAdminLink('Delete Repository Item');
					$this->View->cancel = 'repository';
					$this->View->command = 'deleteRepositoryItem';
					$this->View->module = $this->Model->all_modules[$module_id];
					$this->View->syllabus_id = 'repository';
					$this->View->parseTemplate('page_content', 'syllabus/remove_item.tpl.php');
				}
            } else {
                Messages::addMessage('Invalid Module. Please go to the <a href="repository">Repository Browse Page</a> and try again', 'error');
            }
        } else {
			Messages::addMessage('You do not have permission to access this page.', 'error');
        }
    }

}
