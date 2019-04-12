<?php

/**
 * A hyperlink in a WordML 2003 document
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementHyperLink extends WordDocElement
{
    /**
	 * Whether the run has a style reference in one of it run properties.
	 *
	 * @var string
	 **/
	protected $_hasDocumentedStyle = true;
    
    /**
     * The href for the link.
     *
     * @var string
     */ 
    protected $_href;
    
    /**
     * The bookmark for the link
     *
     * @var string
     */ 
    protected $_bookmark;
    
    /**
     * The title for the link
     *
     * @var string
     */ 
    protected $_title;
    
    /**
     * The styles for the hyper link
     *
     * @var array
     */ 
    protected $_styles;
    
    public function __construct ($parent, $style = null)
    {
        parent::__construct($parent);
        

        if ($parent instanceof WordDocElementRun && ($parentStyle = $parent->getStyles()) != null)
       	{
            $this->_styles = $parentStyle;
            $this->_styles[] = $this->_document->getStyle('HyperLink');
        }
        else
        {
            $this->_styles = array('HyperLink');
        }

        if ($style)
        {
            if (is_string($style))
            {
                $this->_styles[] = $style = $this->_document->getStyle($style);
            }
            else
            {
                $this->_styles[] = $style;
            }
        }
    }
    
    /**
     * return the styles of this hyper link.
     *
     * @return mixed
     */
    public function getStyles ()
    {
        return $this->_styles;
    }
    
    /**
	 * Determine if the run has a run property with a style reference.
	 *
	 * @return bool
	 **/
	public function hasDocumentedStyle ()
	{
		return $this->_hasDocumentedStyle;
	}
    
    /**
     * Create a run as a child element.
     *
     * @param mixed $style
     */
    public function createRun($style = null)
    {
        return $this->createChildElement('Run', $style);
    }
    
    /**
	 * Add text to the paragraph
	 *
	 * @return void
	 * @author Charles O'Sullivan
	 **/
	public function addText ($text)
	{
		return $this->createRun()->addText($text);
	}
    
    /**
     * Set the reference for the hyper link.
     *
     * @param string $reference
     * @return self
     */
    public function setReference ($reference)
    {
        if (strpos($reference, 'http://') === 0)
        {
            $this->_href = $reference;
        }
        else
        {
            $this->_bookmark = $reference;
        }
        return $this;
    }
    
    /**
     * Set a title for the hyper link.
     *
     * @param string $title
     * @return self
     */
    public function setTitle ($title)
    {
        $this->_title = $title;
        return $this;
    }
    
    public function contributeToWordDoc($parent, $insertType = 'append')
    {
    	$hLink = array('w:hlink' => array('#attrs' => array()));
        
        if ($this->_href)
        {
            $hLink['w:hlink']['#attrs']['w:dest'] = $this->_href;
        }
        
        if ($this->_bookmark)
        {
            $hLink['w:hlink']['#attrs']['w:bookmark'] = $this->_bookmark;
        }
        
        if ($this->_title)
        {
            $hLink['w:hlink']['#attrs']['w:screenTip'] = $this->_title;
        }
        
		$hLink = WordDocDomUtils::AppendArrayToXml($parent, $hLink);
		$this->childrenContributeToWordDoc($hLink);
    }
}


?>