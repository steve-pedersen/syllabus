<?php

/**
 * Syllabus Controller
 */
class SyllabusController extends BaseController {

    static $OldSemesterCodes = array(
        1 => 'Winter',
        2 => 'Spring',
        3 => 'Summer',
        4 => 'Fall',
    );

    static $NewSemesterCodes = array(
        1 => 'Winter',
        3 => 'Spring',
        5 => 'Summer',
        7 => 'Fall',
    );

    /**
     * The optional setup() method can be used to handle any child-specific setup that needs to take place before the more
     * generic setup in the BaseController.  For example, overriding the authentication of certain methods must be done here
     */
    public function setup() {
		$this->disable_auth_array[] = 'ping';
		$this->disable_auth_array[] = 'view';
        $this->disable_auth_array[] = 'export';
    }
	
	
	/**
	 * Load a syllabus
	 * @return mixed Returns the result array on success, boolean false on failure
	 */
	private function load() {
		if(isset($this->url_vars[2])) {
			$syllabus = $this->Model->getSyllabusById($this->url_vars[2]);
			$syllabus = $this->mergePost($syllabus);
		}
		
		if(isset($syllabus) && is_array($syllabus) && count($syllabus) > 0) {
			if(preg_match('!^draft-!', $syllabus['syllabus_id'])) {
				$syllabus['syllabus_class_number'] = 'DRAFT';
				$syllabus['syllabus_class_year'] = 'DRAFT';
			}
			
			return $syllabus;
		} else {
			Messages::addMessage('Invalid syllabus', 'error');
			return false;
		}
	}
    

    /**
     * Build the index page.  Depending on the user's permissions, this page will show all viewable syllabi, editable syllabi, or a link to choose
     */
	protected function index() {
        $this->View->addNavLink('All');
        $this->View->page_title = 'My Syllabi';
        $this->View->has_drafts_permission = $this->Permissions->hasPermission(PERM_DRAFTS);
        $D = new SyllabusModel;
        $drafts = $D->getDraftsForUser($_SESSION['user_id']);
        $this->View->drafts = (count($drafts))
            ? $drafts
            : null;
        $syllabi = $this->Model->getSyllabiForUser();

        // echo "<pre>";
        // print_r($syllabi);
        // die;
        
        if(count($syllabi) > 0) {
            $flattened = array();
            foreach ($syllabi as $year => $semesters) {
                foreach ($semesters as $semester => $classes) {
                    foreach ($classes as $syllabus) {
                        // echo "<pre>";
                        // print_r($syllabus);
                        // echo "</pre>";

                        $semesterId = trim($syllabus['syllabus_sem_id']);
                        $length = strlen("$semesterId");
                        $sem = '';

                        switch ($length) {
                            case 5:
                                $sem = self::$OldSemesterCodes[$semester] . ' ' . $year;
                                break;
                            case 4:
                                $sem = self::$NewSemesterCodes[$semester] . ' ' . $year;
                                $semesterId = "$semesterId";
                                $semesterId = $semesterId[0] . '0' . substr($semesterId, 1);
                                break;
                        }

                        if ($semesterId) {
                            if (!isset($flattened[$semesterId])) {
                                $flattened[$semesterId] = array('semester_name' => $sem, 'data' => array());
                            }

                            $flattened[$semesterId]['data'][] = $syllabus;
                        }
                    }
                }
            }

            krsort($flattened);

            // echo "<pre>";
            // print_r($flattened);
            // die;
            $this->View->syllabi = $flattened;
            $this->View->has_syllabi = true;
        } else {
            $this->View->has_syllabi = false;
        }
        $this->View->parseTemplate('page_content', 'syllabus/index.tpl.php');
	}


    /**
     * Build the view page
     */
    protected function view() {		
		// unset($_SESSION['allow_temporary_view']);
		if(false != ($syllabus = $this->load())) {
            // if there is a token in the URL, try to set it
            if(isset($_GET['token'])) {
                $this->Model->registerViewToken($syllabus['syllabus_id'], $_GET['token']);
            }
			
			$syllabus['syllabus_phone'] = Utility::formatPhoneNumber($syllabus['syllabus_phone'], 'print');
			$syllabus['syllabus_mobile'] = Utility::formatPhoneNumber($syllabus['syllabus_mobile'], 'print');
			$syllabus['syllabus_fax'] = Utility::formatPhoneNumber($syllabus['syllabus_fax'], 'print'); 
            $syllabus['semester_name'] = $this->get_semester_name($syllabus['syllabus_sem_id'], $syllabus['syllabus_class_semester']);
			
            $this->View->syllabus = $syllabus;
            $this->View->page_title = $syllabus['syllabus_class_number'];
            $this->View->page_header = $syllabus['syllabus_class_title'];
            $this->View->addNavLink($this->View->page_title);
            if($this->Permissions->canViewSyllabus($syllabus['syllabus_id'])) {
                $this->View->can_edit = ($this->Permissions->canEditSyllabus($syllabus['syllabus_id']));
                $this->View->edit_mode = false;
                
                $enabled_modules = $this->Model->getModulesForSyllabus($syllabus['syllabus_id']);
                
                foreach($enabled_modules as $k => $v) {
                    $module_type = $k;
                    $method_name = 'get' . ucwords(strtolower($module_type)) . 'ForSyllabus';
                    $result = $this->Model->{$method_name}($syllabus['syllabus_id']);
                    $this->View->$k = $result;
                }
                // after the other modules are built, merge the general module to the beginning
                // can't do this earlier because there is no getGeneralForSyllabus() method and the script will error out.
                $general_module = array('general' => true);
                $enabled_modules = (is_array($enabled_modules))
                    ? array_merge($general_module, $enabled_modules)
                    : $general_module;
                $this->View->enabled_modules = $enabled_modules;
                
                if(isset($_GET['view']) && $_GET['view'] == 'print') {
                    $this->View->setView('print');
                }
                
                $this->View->parseTemplate('page_content', 'syllabus/view.tpl.php');
                
            } else {
                $this->View->parseTemplate('page_content', 'syllabus/request_view_token.tpl.php');
            }
        }
    }
    
    
    /**
     * Set the view for the printer-friendly version
     */
    public function export() {
		if(false != ($syllabus = $this->load())) {
			if($this->Permissions->canExportSyllabus($syllabus['syllabus_id'])) {
				if(!isset($_GET['export_msg'])) {
					$Word = new SyllabusWordRenderer($syllabus['syllabus_id']);
				} else {
					$this->View->export = 'syllabus/export/' . $syllabus['syllabus_id'];
					$this->View->cancel = $_GET['ref'];
					$this->View->parseTemplate('page_content', 'syllabus/word_message.tpl.php');
				}
			} else {
				Messages::addMessage('You do not have permission to export this syllabus.', 'error');
			}
		}
    }


    /**
     * Build the edit syllabus view
     */
    protected function edit() {
        if(false != ($syllabus = $this->load())) {
			$syllabus['syllabus_phone'] = Utility::formatPhoneNumber($syllabus['syllabus_phone'], 'print');
			$syllabus['syllabus_mobile'] = Utility::formatPhoneNumber($syllabus['syllabus_mobile'], 'print');
			$syllabus['syllabus_fax'] = Utility::formatPhoneNumber($syllabus['syllabus_fax'], 'print');
            $syllabus['semester_name'] = $this->get_semester_name($syllabus['syllabus_sem_id'], $syllabus['syllabus_class_semester']);
			
            $this->View->syllabus = $syllabus;
			
            $this->View->syllabus_id = $syllabus['syllabus_id'];
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
                $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
                $this->View->addNavLink('Edit');
                $this->View->page_header = $syllabus['syllabus_class_title'];
                $this->View->page_title = $syllabus['syllabus_class_number'];
                $this->View->class_name = $syllabus['syllabus_class_number'];
                $this->View->edit_mode = true;
                
                $R = new RepositoryModel();
                $repository = $R->getRepositoryItems();
                foreach($repository as $k => $v) {
                    if(!count($v['items'])) {
                        unset($repository[$k]);
                    }
                }
                $this->View->repository = $repository;
                
                $S = new SyllabusModel();     
                $enabled_modules = $S->getModulesForSyllabus($syllabus['syllabus_id']);
				
                $add_modules = $this->Model->all_modules;
                
                foreach($enabled_modules as $k => $v) {
                    unset($add_modules[$v['module_type']]);
                    $method_name = 'get' . ucwords(strtolower($k)) . 'ForSyllabus';
                    $result = $this->Model->{$method_name}($syllabus['syllabus_id']);
					$this->View->$k = $result;
                }
                
                $this->View->add_modules = $add_modules;
                $this->View->enabled_modules = $enabled_modules;                
                $this->View->parseTemplate('page_content', 'syllabus/edit.tpl.php');
            } else {
				Messages::addMessage('You do not have permission to edit this syllabus', 'error');
            }
        }
    }
    
    
    /**
     * Create a draft
     */
    protected function draft() {
        if($this->Permissions->hasPermission(PERM_DRAFTS)) {
            $this->View->is_draft = true;
			$syllabus_id = 'draft-' . md5($_SESSION['user_id'] . '-' . time());
			$syllabus = $this->mergePost(array('syllabus_id' => $syllabus_id));
			$this->View->syllabus_id = $syllabus_id;
			$this->View->syllabus = $syllabus;
            $this->View->addNavLink('Create Draft');
            $this->View->page_header = "Create Draft Syllabus";
            $this->View->page_title = "Create Draft Syllabus";
            $this->View->command = 'createDraft';
            $this->View->cancel = 'syllabus';
            $this->View->parseTemplate('page_content', 'modules/general/form.tpl.php');
        } else {
            Messages::addMessage('You do not have permission to create drafts.', 'error');
        }
    }
    
    
    /**
     * Edit the general info for the syllabus
     */
    protected function edit_info() {
        if(false != ($syllabus = $this->load())) {
			$syllabus['syllabus_phone'] = Utility::formatPhoneNumber($syllabus['syllabus_phone'], 'print');
			$syllabus['syllabus_mobile'] = Utility::formatPhoneNumber($syllabus['syllabus_mobile'], 'print');
			$syllabus['syllabus_fax'] = Utility::formatPhoneNumber($syllabus['syllabus_fax'], 'print');
            $this->View->syllabus = $syllabus;
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
                $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
                $this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
                $this->View->addNavLink('Edit Info');
                $this->View->page_header = $syllabus['syllabus_class_number'];
                $this->View->page_title = $syllabus['syllabus_class_number'];
				$this->View->syllabus_id = $syllabus['syllabus_id'];
                $this->View->command = 'editSyllabus';
                $this->View->is_draft = (preg_match('!draft-!', $syllabus['syllabus_id'])) ? true : false;
                $this->View->return_url = 'syllabus/' . $syllabus['syllabus_id'] . '/edit';
                $this->View->cancel = 'syllabus/' . $syllabus['syllabus_id'] . '/edit';
                $this->View->parseTemplate('page_content', 'modules/general/form.tpl.php');
            } else {
                Messages::addMessage('You do not have permission to edit this syllabus.', 'error');
            }
        }
    }
    
    
    /**
     * Add a module
     */
    protected function add_module() {
        if(false != ($syllabus = $this->load())) {
            $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Add Module');
            $this->View->page_header = 'Add Module';
            $this->View->page_title = 'Add Module';
            $this->View->command = 'addModule';
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
                $add_modules = $this->Model->all_modules;
                $current_modules = $this->Model->getModulesForSyllabus($syllabus['syllabus_id']);
                foreach($current_modules as $k => $v) {
                    unset($add_modules[$k]);
                }
                $this->View->add_modules = $add_modules;
                $this->View->syllabus = $syllabus;
                $this->View->parseTemplate('page_content', 'syllabus/edit_module.tpl.php');
            } else {
                Messages::addMessage('You do not have permission to edit the selected syllabus', 'error');
            }
        }
    }
    
    
    /**
     * Edit a module
     */
    protected function edit_module() {
        if(false != ($syllabus = $this->load())) {
            $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit Module');
            $this->View->page_header = 'Edit Module';
            $this->View->page_title = 'Edit Module';
            $this->View->command = 'editModule';
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
                $this->View->syllabus = $syllabus;
                if(isset($this->url_vars[3]) && array_key_exists($this->url_vars[3], $this->Model->all_modules)) {
                    $this->View->module = $this->Model->all_modules[$this->url_vars[3]];
                    $this->View->module_custom_name = $this->Model->getCustomModuleName($syllabus['syllabus_id'], $this->url_vars[3]);
                    $this->View->parseTemplate('page_content','syllabus/edit_module.tpl.php');
                } else {
                    Messages::addMessage('Invalid Module.  Please return to the <a href="syllabus/edit/' . $this->url_vars[2] . '">Edit Syllabus</a> page to select a madule.', 'error');
                }
            } else {
               Messages::addMessage('You do not have permission to edit the selected syllabus', 'error');
            }
        }
    }
    
    
    /**
     * Remove a module
     */
    protected function remove_module() {
        if(false != ($syllabus = $this->load())) {
            $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Remove Module');
            $this->View->syllabus = $syllabus;
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
                $this->View->page_title = 'Remove Module From Syllabus';
                $this->View->module_type = $this->url_vars[3];
                $this->View->parseTemplate('page_content', 'syllabus/remove_module.tpl.php');
            } else {
                Messages::addMessage('You do not have permission to edit the selected syllabus', 'error');
            }
        }
    }
    
    
    /**
     * Add an item
     */
    protected function add_item() {
        if(false != ($syllabus = $this->load())) {
            $module = $this->Model->all_modules[$this->url_vars[3]];
            $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Add ' . $module['singular']);
            $this->View->page_header = 'Add ' . $module['singular'];
            $this->View->page_title = 'Add ' . $module['singular'];
            $this->View->syllabus = $syllabus;
            $this->View->command = 'addItem';
            $this->View->cancel = 'syllabus/edit/' . $syllabus['syllabus_id'];
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
                $this->View->syllabus_id = $syllabus['syllabus_id'];
                $this->View->module = $module;
                $this->View->parseTemplate('page_content', 'modules/' . $module['id'] . '/form.tpl.php');
            } else {
                Messages::addMessage('You do not have permission to edit the selected syllabus', 'error');
            }
        }
    }
    
    
    /**
     * Edit an item
     */
    protected function edit_item() {
        if(false != ($syllabus = $this->load())) {
            $module = $this->Model->all_modules[$this->url_vars[3]];
            $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit ' . $module['singular']);
            $this->View->page_header = 'Edit ' . $module['singular'];
            $this->View->page_title = 'Edit ' . $module['singular'];
            $this->View->syllabus = $syllabus;
			$this->View->module = $module;
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
				$item = $this->Model->getModuleItem($module['id'], $this->url_vars[4]);
				$this->View->syllabus_id = $syllabus['syllabus_id'];
				$this->View->command = 'editItem';
				$this->View->cancel = 'syllabus/edit/' . $syllabus['syllabus_id'];
                $this->View->item = $item;
                $this->View->parseTemplate('page_content', 'modules/' . $module['id'] . '/form.tpl.php');
            } else {
               Messages::addMessage('You do not have permission to edit the selected item', 'error');
            }
        }
    }
    
    
    /**
     * Remove an item
     */
    protected function remove_item() {
        if(false != ($syllabus = $this->load())) {
            $module = $this->Model->all_modules[$this->url_vars[3]];
            $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Remove ' . $module['singular']);
            $this->View->page_header = 'Remove ' . $module['singular'];
            $this->View->page_title = 'Remove ' . $module['singular'];
            $this->View->syllabus = $syllabus;
			$this->View->module = $module;
            $this->View->return_url = 'syllabus/' . $syllabus['syllabus_id'] . '/edit';
            
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
                $item = $this->Model->getModuleItem($module['id'], $this->url_vars[4]);
				$this->View->syllabus_id = $syllabus['syllabus_id'];
				$this->View->cancel = 'syllabus/' . $syllabus['syllabus_id'] . '/edit';
				$this->View->command = 'removeItem';
                $this->View->item_id = $this->url_vars[4];
                $this->View->parseTemplate('page_content', 'syllabus/remove_item.tpl.php');
            } else {
                Messages::addMessage('You do not have permission to remove the selected item', 'error');
            }
        }
    }
    
    
    /**
     * Add a prebuilt repository item to a syllabus
     */
    protected function add_from_repository() {
        if(false != ($syllabus = $this->load())) {
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
				$module = $this->Model->all_modules[$this->url_vars[3]];
				$this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
				$this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
				$this->View->addNavLink('Add Repository Item');
                $this->View->page_title = 'Add an item from the Repository';
                $this->View->page_header = 'Add ' . $module['singular'] . ' From Repository';
                $this->View->cancel = 'syllabus/edit/' . $syllabus['syllabus_id'];
                $this->View->command = 'addItemFromRepository';
                $this->View->syllabus = $syllabus;
				
                $R = new RepositoryModel();
                $repository = $R->getRepositoryItems($module['id']);
                $this->View->m = (count($repository[$this->url_vars[3]]['items']) > 0)
                    ? $repository
                    : false;
                $this->View->m = $repository[$this->url_vars[3]];
                $this->View->parseTemplate('page_content', 'modules/module_add_from_repository.tpl.php');
            } else {
                Messages::addMessage('You do not have permission to remove the selected item', 'error');
            }
        }
    }


    /**
     * Set sharing options
     */
    protected function share() {
        if(false != ($syllabus = $this->load())) {
            $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Sharing');
            $this->View->page_header = 'Edit Sharing';
            $this->View->page_title = 'Share Syllabus';
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
                $this->View->syllabus = $syllabus;
                $E = new SyllabusModel();
                $editors = $E->getEditorsForSyllabus($syllabus['syllabus_id']);
                $this->View->editors = $editors;
                $this->View->parseTemplate('page_content', 'syllabus/share.tpl.php');
            } else {
                Messages::addMessage('You do not have permission to edit sharing options for this syllabus.', 'error');
            }
        }
    }
	
    
    /**
     * Ping the syllabus.  This is the entrance point for external services to gather information about a syllabus
     */
    protected function ping() {
        $return_array = array();
        if(false != ($syllabus = $this->load())) {
            $M = new SyllabusModel();
            $modules = $M->getModulesForSyllabus($syllabus['syllabus_id']);
            $return_array['exists'] = true;
            $return_array['url'] = BASEHREF . 'syllabus/view/' . $syllabus['syllabus_id'];
            if($syllabus['syllabus_visibility'] == 'members') {
                $return_array['password'] = $syllabus['syllabus_view_token'];
            }
            $return_array['edited'] = count($modules) > 0 ? true : false;
            $return_array['visible'] = ($syllabus['syllabus_visibility'] != 'editors') ? true : false;
        } else {
            $return_array['exists'] = false;
        }
        $return_json = json_encode($return_array);
        echo($return_json);
        exit;
    }
    
    
    /**
     * Backup and Restore page
     */
    protected function backup_restore() {
        if(false != ($syllabus = $this->load())) {
            $this->View->syllabus = $syllabus;
            $this->View->all_modules = $this->Model->all_modules;
            $this->View->syllabus_id = $syllabus['syllabus_id'];
            $this->View->page_title = 'Backup and Restore';
            $this->View->page_header = 'Backup and Restore';
            $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Backup and Restore');
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
                $this->View->parseTemplate('page_content', 'syllabus/backup_restore.tpl.php');
            } else {
                Messages::addMessage('You do not have permission to edit this syllabus.', 'error');
            }
        }
    }
    
    
    /**
     * Reset the syllabus view token
     */
    protected function reset_token() {
        if(false != ($syllabus = $this->load())) {
            $this->View->syllabus = $syllabus;
            $this->View->syllabus_id = $syllabus['syllabus_id'];
            $this->View->page_title = 'Reset Syllabus Password';
            $this->View->addNavLink($syllabus['syllabus_class_number'], 'syllabus/view/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Edit', 'syllabus/edit/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Share', 'syllabus/share/' . $syllabus['syllabus_id']);
            $this->View->addNavLink('Reset Password');
            if($this->Permissions->canEditSyllabus($syllabus['syllabus_id'])) {
                $this->View->parseTemplate('page_content', 'syllabus/reset_token.tpl.php');
            } else {
                Messages::addMessage('You do not have permission to reset the password for this syllabus.', 'error');
            }
        }
    }


    private function get_semester_name($semesterId, $semesterNumber) {
        $semesterId = trim($semesterId);
        $length = strlen("$semesterId");
        $name = '';
        switch ($length) {
            case 5:
                $name = self::$OldSemesterCodes[$semesterNumber];
                break;
            case 4:
                $name = self::$NewSemesterCodes[$semesterNumber];
                break;
        }

        return $name;
    }

}


?>