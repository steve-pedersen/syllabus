<?php

/**
 * Create appropriate Ajax reponses for Ajax submissions
 */
class AjaxResponse { 
    
    /**
     * @var array Array to be returned at the end of the Ajax script
     */
    private $response = array('close_colorbox' => true);
	
	
	/**
	 * Constructor
	 */
	public function __construct($M, $V) {
		$this->Model = $M;
		$this->View = $V;
	}
	
	
	/**
	 * Print the response
	 */
	public function printResponse() {
		$this->response['message'] = Messages::getMessagesHtml();
		echo(json_encode($this->response));
	}
    
	
	/**
	 * If there was an error in submitting the form
	 */
	public function formSubmissionError() {
		$this->response['update_id'] = 'colorbox-content';
		$this->response['update_method'] = 'prepend';
		$this->response['close_colorbox'] = false;
		$this->response['update_html'] = Messages::getMessagesHtml();
		Messages::clearMessageArray();
	}
	
   
   /**
    * Custom return script for editing general syllabus info
    */
   public function editSyllabus() {
        $syllabus = $this->Model->getSyllabusById($this->Model->syllabus_id);
		/*
		// modify vars as necessary
		$syllabus['syllabus_phone'] = Utility::formatPhoneNumber($syllabus['syllabus_phone'], 'print');
		$syllabus['syllabus_mobile'] = Utility::formatPhoneNumber($syllabus['syllabus_mobile'], 'print');
		$syllabus['syllabus_fax'] = Utility::formatPhoneNumber($syllabus['syllabus_fax'], 'print');		
        $this->View->syllabus = $syllabus;
		$this->View->class_name = (preg_match('!^draft-!', $syllabus['syllabus_id'])) ? 'DRAFT' : $syllabus['syllabus_class_number'];
		
        $this->View->parseTemplate('update_html', 'modules/general/item.tpl.php');
        $this->response['update_id'] = 'general_item_content';
        $this->response['update_method'] = 'replace_contents';
        $this->response['update_html'] = $this->View->update_html;
		*/
		
		// switched response to a redirect since it can potentially update multiple areas on the page
		// and the ajax response isn't currently configured to modify multiple nodes
		$this->response['update_method'] = 'redirect';
		$this->response['redirect'] = BASEHREF . 'syllabus/edit/' . $syllabus['syllabus_id'];
    }
   
    /**
     * Custom return script for adding modules
    public function addModule($query_result) {
        $this->response['update_id'] = $_POST['module_type'] . '_title';
        $this->response['update_method'] = 'append';
        $this->response['update_html'] = $_POST['module_custom_name'];
    }
     */
    
    
    /**
     * Custom return script for editing modules
     */
    public function editModule() {
        $this->response['update_id'] =  $this->Model->module_type . '_title';
        $this->response['update_method'] = 'replace_contents';
        $this->response['update_html'] = $this->Model->module_custom_name;
    }
    
    
    /**
     * Custom return script for removing modules
     */
    public function removeModule() {
        $this->response['update_id'] = $_POST['module_type'] . '_module';
        $this->response['update_method'] = 'remove';
    }
    
    
    /**
     * Custom return script for adding items
     */
    public function addItem() {
		$module = $this->Model->module_type;
		$insert_id = $this->Model->result['insert_id'];
		
		switch($module) {
			case 'assignments': $item = $this->Model->getAssignmentById($insert_id); break;
			case 'materials': $item = $this->Model->getMaterialById($insert_id); break;
			case 'methods': $item = $this->Model->getMethodById($insert_id); break;
			case 'objectives': $item = $this->Model->getObjectiveById($insert_id); break;
			case 'policies': $item = $this->Model->getPolicyById($insert_id); break;
			case 'schedules': $item = $this->Model->getScheduleById($insert_id); break;
			case 'tas': $item = $this->Model->getTaById($insert_id); break;
			default: break;
		}
		
		$this->View->i = $item;
		$this->View->item_id = $insert_id;
		$this->View->module = $module;
		$this->View->syllabus_id = $this->Model->syllabus_id;
		$this->View->parseTemplate('update_html', 'modules/' . $module . '/item.tpl.php');
		$this->response['update_id'] = $module . '_items';
		$this->response['update_method'] = 'append';
		$this->response['update_html'] = $this->View->update_html;
    }
    
    
    /**
     * Custom return script for editing items
     */
    public function editItem() {
		$module = $this->Model->module_type;
		$item_id = $this->Model->item_id;
		
		switch($module) {
			case 'assignments': $item = $this->Model->getAssignmentById($item_id); break;
			case 'materials': $item = $this->Model->getMaterialById($item_id); break;
			case 'methods': $item = $this->Model->getMethodById($item_id); break;
			case 'objectives': $item = $this->Model->getObjectiveById($item_id); break;
			case 'policies': $item = $this->Model->getPolicyById($item_id); break;
			case 'schedules': $item = $this->Model->getScheduleById($item_id); break;
			case 'tas': $item = $this->Model->getTaById($item_id); break;
			default: break;
		}
		
        $this->View->i = $item;
		$this->View->item_id = $item_id;
        $this->View->edit_mode = true;
		$this->View->module = $module;
		$this->View->syllabus_id = $this->Model->syllabus_id;
        $this->View->parseTemplate('update_html', 'modules/' . $module . '/item.tpl.php');
        $this->response['update_id'] = strtolower($this->Model->all_modules[$module]['singular']) . '_' . $item_id;
        $this->response['update_method'] = 'replace';
        $this->response['update_html'] = $this->View->update_html;
    }
    
    
    /**
     * Custom return script for removing items
     */
    public function removeItem() {
		$module = $this->Model->module_type;
		$item_id = $this->Model->item_id;
        $this->response['update_id'] = strtolower($this->Model->all_modules[$module]['singular']) . '_' . $item_id;
        $this->response['update_method'] = 'remove';
    }


}