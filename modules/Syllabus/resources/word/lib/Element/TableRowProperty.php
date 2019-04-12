<?php
	
/**
 * A table row property in a WordML 2003 document.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementTableRowProperty extends WordDocElement
{   
    public function __construct ($parent)
    {
        parent::__construct($parent, 'w:trPr');
    }
    
    /**
     * Set the style of the row.
     *
     * @param mixed $style
     * @return self
     */
	public function setStyle ($style)
    {
        if (is_string($style))
        {
            $style = $this->_document->getStyle($style);
        }
        
        if ($style->getTableRowProperty()->hasChildren())
        {
            $this->addChildren($style->getTableRowProperty()->getChildren());
        }
        
        return $this;
    }
}

    
?>