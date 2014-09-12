<?php

/**
 * Base class for all models.  Class includes wrapper methods for forming and executing queries
 */
class BaseModel {
    
    /**
     * @var array Data array for accessing and writing to private variables
     */
    private $data = array();
 
	/**
	 * @var object The mysqli object
	 */
	protected $mysqli;
 
    /**
     * @var string The query string to be executed
     */
    protected $query = '';

    /**
     * @var array Array that holds all information and results for the query
     */
    public $result = array();

    /**
     * @var object Permissions object
     */
    public $Permissions;
	
    /**
     * @var array Array of all modules currently available in the system.
     * Array keys are the machine readable names. Array values are the plain text names of the modules
     */
    public $all_modules = array(
        'assignments' => array('id' => 'assignments', 'name' => 'Assignments', 'singular' => 'Assignment', 'db_name' => 'assignments', 'db_prefix' => 'assignment'),
        'materials' => array('id' => 'materials', 'name' => 'Materials', 'singular' => 'Material', 'db_name' => 'materials', 'db_prefix' => 'material'),
        'methods' => array('id' => 'methods', 'name' => 'Methods', 'singular' => 'Method', 'db_name' => 'methods', 'db_prefix' => 'method'),
        'objectives' => array('id' => 'objectives', 'name' => 'Objectives', 'singular' => 'Objective', 'db_name' => 'objectives', 'db_prefix' => 'objective'),
        'policies' => array('id' => 'policies', 'name' => 'Policies', 'singular' => 'Policy', 'db_name' => 'policies', 'db_prefix' => 'policy'),
        'schedules' => array('id' => 'schedules', 'name' => 'Schedules', 'singular' => 'Schedule', 'db_name' => 'schedules', 'db_prefix' => 'schedule'),
        'tas' => array('id' => 'tas', 'name' => 'Teaching Assistants', 'singular' => 'TA', 'db_name' => 'teaching_assistants', 'db_prefix' => 'ta')
    );

    
    
    /**
     * Constructor.
     * @param object $P Permissions object
     */
	public function __construct($P = null) {
		// set the permissions object
        if(isset($P) && is_object($P)) {
			$this->Permissions = $P;
		}
		// instantiate a mysqli object
		$this->mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	}


    /**
     * Magic function for checking if a variable is set
     * @param string $k Name of the variable to check
     * @return bool True if set, false if not set
     */
	public function __isset($k) {
		return (isset($this->data[$k])) ? true : false;
	}


    /**
     * Magic function for getting variables
     * @param string $k Key name of the variable to be accessed
     * @return mixed Value of the requested variable
     */
	public function __get($k) {
		if(isset($this->data[$k])) {
			return $this->data[$k];
		} else {
			return null;
		}
	}


    /**
     * Magic function for setting variables. This 
     * within the Model and run the submitted data through them.
     * @param string $k Name of the variable to be set
     * @param mixed $v Value to set to the variable
     */
	public function __set($k, $v) {
		$valid = true;
		// run custom validation function if exists
		if(method_exists('Validate', $k)) {
			$valid = Validate::$k($v);
		}
		// sanitize
		if($valid) {
			$this->data[$k] = $this->sanitize($v, $k);
		}
		
		return $valid;
	}
	

	/**
	 * Sanitize the data
	 * @param mixed $v The data to be sanitized
	 * @param string $k The name of the variable
	 * @return mixed The sanitized data
	 */
	private function sanitize($v, $k=null) {
		switch(true) { 
			case is_string($v):
				Validate::stripTags($k, $v);
				$v = $this->mysqli->real_escape_string($v);				
				break;
			
			case is_array($v):
				$v = array_map(array($this, 'sanitize'), $v);
				break;
			
			default:
				break;
		}
		
		return $v;
	}


    /**
     * Execute the prepared query
     * @return mixed If the query results in a resource, function returns an array of all result rows on success, false on failure.
     * If the query does not create a resource, function returns true on success, false on failure.
     */
	public function executeQuery() {
		// reset the result array
		$this->result = array();
		if(false !== ($result = $this->mysqli->query($this->query))) {
			if(is_object($result)) {
				$this->result['count'] = $result->num_rows;
				while($row = $result->fetch_array(MYSQLI_ASSOC)) {
					$this->result['data'][] = $row;
				}
			}
			$this->result['query'] = $this->query;
			$this->result['insert_id'] = $this->mysqli->insert_id;
			$this->result['affected'] = $this->mysqli->affected_rows;
			return $this->result;
		} else {
			if(DEBUG_MODE) {
				Messages::printMessage('Query error: ' . $this->mysqli->error . '<p>' . $this->query . '</p>', 'error');
			}
			$return = false;
		}
	}
	/* Pass an array of queries that are a part of a transaction*/
	public function transaction ($Q){

		//this is the same as begin_transaction()
		$this->mysqli->autocommit(FALSE);
		

       for ($i = 0; $i < count ($Q); $i++){
           if (!$this->mysqli->query($Q[$i])){
               if(DEBUG_MODE) {
				Messages::printMessage('Query error transaction: ' . $this->mysqli->error . '<p>' . $Q[$i] . '</p>', 'error');
				}
               break;
           }       
       }

       if ($i == count ($Q)){
           $this->mysqli->commit();
           return 1;
       }
       else {
           $this->mysqli->rollback();
           return 0;
       }
   }


}