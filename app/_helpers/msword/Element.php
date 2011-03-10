<?php
	
/**
 * A base class for object representations of WordML elements.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
abstract class WordDocElement implements WordDocIContributor
{
    /**
     * The name of the dom element
     *
     * @var string
     */ 
    protected $_name;
    
    /**
     * The parent Element of the element
     *
     * @var WordDocElement
     */ 
    protected $_parent;
    
    /**
     * The children of this element
     *
     * @var array of WordDocElement
     */ 
    protected $_children;
    
    /**
     * The attributes of this element
     *
     * @var array
     */ 
    protected $_attributes;

	/**
	 * The document object this element belongs to
	 *
	 * @var WordDocDocument
	 **/
	protected $_document;
    
    /**
     * Create a new word doc element and return it.
     *
     * @param string $elementName
     * @return WordDocElement
     */
    public static function GetNewElement ($elementName)
    {
        $args = func_get_args();
        array_shift($args);
        
        $element = null;
        $elementPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Element' . DIRECTORY_SEPARATOR . $elementName . '.php';
        $elementClass = 'WordDocElement' . $elementName;
        
        if (file_exists($elementPath))
        {
            require_once($elementPath);
            $reflectionClass = new ReflectionClass($elementClass);
            $element = $reflectionClass->newInstanceArgs($args);
        }
        
        return $element;
    }
    
	public function __construct ($parent, $name = '')
    {
        $this->_name = $name;
        $this->_parent = $parent;
		$this->_document = ($parent ? $parent->getDocument() : null);
        $this->_children = array();
    }

	/**
	 * Retrieve the parent element
	 *
	 * @return WordDocElement
	 **/
	public function getParent ()
	{
		return $this->_parent;
	}
    
    /**
     * Return the document this element is a part of.
     *
     * @return WordDocDocument
     */
	public function getDocument ()
	{
		return $this->_document;
	}
    
    /**
     * Retrieve the child elements (or clones of them) from this element.
     * 
     * @return array
     */
    public function getChildren ($clone = true)
    {
        $children = array();

        if ($clone)
        {
            foreach ($this->_children as $child)
            {
                $children[] = clone $child;
            }
        }
        else
        {
            $children = $this->_children;
        }
        
        return $children;
    }
    
    /**
     * Add children to this element.
     *
     * @param array $children
     * @return self
     */
    public function addChildren ($children)
    {
        $this->_children = array_merge($this->_children, $children);
        return $this;
    }
    
    /**
     * Add a child to the element.
     *
     * @param WordDocElement $child
     * @return self
     */
    public function addChild ($child)
    {
        $this->_children[] = $child;
        return $this;
    }
    
    /**
     * Determine if this element has children.
     *
     * @return boolean
     */
    public function hasChildren ()
    {
        return !empty($this->_children);
    }
    
    /**
     * Add html to the element.
     *
     * @param string $html
     * @return self
     */
    public function addHtml ($html)
    {
        $parser = new WordDocHtmlParser();
        $parser->parse($html, $this);
        return $this;
    }
    
    /**
     * Set an attribute for this element
     *
     * @param string $key
     * @param string $value
     * @return self
     */
    public function setAttribute($key, $value)
    {
        $this->_attributes[$key] = $value;
        return $this;
    }
    
    /**
     * Create a new element and add it as a child.
     *
     * @param $type - The type of element to create
     */
    public function createChildElement ($type)
    {
       $args = func_get_args();
       array_shift($args);
       array_unshift($args, $type, $this);
       $this->_children[] = $element = call_user_func_array(array('WordDocElement', 'GetNewElement'), $args);
       return $element;
    }
    
    /**
     * Create an annotation.
     * 
     * @param string $name
     * @return WordDocElementAnnotation
     */
    public function createAnnotation ($name)
    {
        return $this->createChildElement('Annotation', $name);
    }
    
    /**
     * Return to the parent element.
     * 
     * @return mixed
     */
    public function end ()
    {
        return $this->_parent;
    }
    
    /**
     * Contribute XML to the Word document by inserting XML into the parent
     *
     * @param DOMNode $parent
     * @param string $insertType - The values can be: 'append' or 'prepend'
     */
	public function childrenContributeToWordDoc ($parent, $insertType = 'append')
    {
        if ($this->_children)
        {
            foreach ($this->_children as $element)
            {
                $element->contributeToWordDoc($parent, $insertType);
            }
        }
    }
    
    public function contributeToWordDoc ($parent, $insertType = 'append')
    {
        if ($this->_attributes)
        {
            $elt = WordDocDomUtils::AppendArrayToXML($parent, array($this->_name => array('#attrs' => $this->_attributes)));
        }
        else
        {
            $elt = WordDocDomUtils::AppendArrayToXML($parent, array($this->_name => ''));
        }
        
        $this->childrenContributeToWordDoc($elt, $insertType);
    }
    
    public function __clone ()
    {
        foreach ($this as $key => $val) 
        {
			if ($key == '_parent' || $key == '_document' || $key == '_children') continue;
			
            if (is_object($val) || (is_array($val))) 
            {
                $this->{$key} = unserialize(serialize($val));
            }
        }
		
		$this->_document = $this->_document;
    }
}
    
?>