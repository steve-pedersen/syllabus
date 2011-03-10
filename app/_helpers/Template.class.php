<?php

/*
 * class Template
 * Class for rendering the template. The wrapper class pulls together all the necessary information for building the view,
 * assigns variables appropriately based off the template engine, and outputs the view. 
 */
class Template {
    
    /**
     * @var object Smarty object
     */
    private $smarty;
   
    /**
     * @var string Master view to use (different views could include normal, printer-friendly, etc.)
     */
    private $view = '_views/default.tpl.php';
	
	/**
	 * @var array Array of links for the default navigation
	 */
	private $site_nav_array = array(
		array('text' => 'My Syllabi', 'link' => 'syllabus')
	);
	
	/**
	 * @var array Array of links for the admin navigation
	 */
	private $admin_nav_array = array(
	);

    
    /**
     * __construct()
     * sets the Registry and does initial setup of the templating engine
     * @param object $R the Registry object
     */
    function __construct() {
        require('Smarty.class.php');
        $this->smarty = new Smarty;
        $this->smarty->template_dir = SMARTY_TEMPLATES_DIR;
        $this->smarty->compile_dir = SMARTY_TEMPLATES_C_DIR;
        $this->smarty->cache_dir = SMARTY_CACHE_DIR;
        $this->smarty->config_dir = SMARTY_CONFIGS_DIR;
    }
    
    
    /**
     * Assign value to a template variable
     * @param string $key Name of the variable to be assigned
     * @param mixed $value Value to assign to the variable
     */
    function __set($key, $value) {
        $this->smarty->assign($key, $value);
    }
    
    
    /**
     * Retrieve template variables
     * @param string $key The name of the variable to return
     */
    function __get($key) {
        return $this->smarty->get_template_vars($key);   
    }
	
	
	/**
	 * Sets the view to use for the current template
	 * @param string $v The view to set
	 */
	public function setView($v) {
		switch($v) {
			case 'ajax': 	$view = '_views/ajax.tpl.php'; 		break;
			case 'feed': 	$view = '_views/feed.tpl.php';		break;
			case 'print': 	$view = '_views/print.tpl.php'; 	break;
			default:		$view = $this->view;				break;
		}
		$this->view = $view;
	}
    
    
    /**
     * Parse the given template and return the resulting string
     * @param string $var Name to assign the parsed template string to
     * @param string $template Path to the template to be parsed (relative to the Smarty templates directory)
     * @return string The string of the parsed template
     */
    public function parseTemplate($var, $template) {
		$string = $this->smarty->fetch($template);
		$this->$var = $string;
    }


    /**
     * Add an element to the breadcrumbs array
     * @param string $text Text to add to the breadcrumbs
     * @param string $link Optional path the breadcrumb should link to
     */
	public function addNavLink($text, $link = NULL) {
        array_push($this->site_nav_array, array('text' => $text, 'link' => $link));
	}


    /**
     * Add an element to the breadcrumbs array
     * @param string $text Text to add to the breadcrumbs
     * @param string $link Optional path the breadcrumb should link to
     */
	public function addAdminLink($text, $link = NULL) {
        array_push($this->admin_nav_array, array('text' => $text, 'link' => $link));
	}
    
    
    /**
     * Build the breadcrumb links from the array
     * @return string HTML for the navigation bar(s)
     */
    private function buildNavigation() {
		$this->site_nav = $this->site_nav_array;
		$this->admin_nav = $this->admin_nav_array;
		$this->parseTemplate('breadcrumbs', '_fragments/navigation.tpl.php');
    }
	
	
	/**
	 * Build the CSS string
	 * @return string HTML string to include all the necessary CSS files
	 */
	private function buildCss() {
		$css_array = explode(',' , str_replace(' ', '', CSS_PATHS));
		$css_includes = Compiler::compileCss($css_array);
		$this->css_includes = $css_includes;
	}
	
	
	/**
	 * Build the JS string
	 * @return string HTML string to include all the necessary JS files
	 */
	private function buildJs() {
		$js_array = explode(',' , str_replace(' ', '', JS_PATHS));
		$js_includes = Compiler::compileJs($js_array);
		$this->js_includes = $js_includes;
	}


    /**
     * Run any necessary actions prior to running the render() method.  This includes setting template variables, creating
     * the appropriate CSS and JS include strings, and parsing template fragments into string format and assigning them
     * to template variables for use in the master template.
     */
    private function beforeRender() {
        $this->buildNavigation();
		$this->buildCss();
		$this->buildJs();
    }
    
    
    /**
     * Render the view.  This method first calls beforeRender() to do any final preparation for the view
     * and then renders the master template
     */
    public function render() {
        $this->beforeRender();
        $this->smarty->display($this->view);
    }

}