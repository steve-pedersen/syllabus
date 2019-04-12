<?php

/**
 * A sub-section element in WordML
 * 
 * @schema http://schemas.microsoft.com/office/word/2003/auxHint
 * @element sub-section
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementSubSection extends WordDocElement
{
    public function __construct ($parent)
    {
        parent::__construct($parent, 'wx:sub-section');
    }
    
    /**
     * Create a new paragraph in this section
     *
     * @param $style mixed
     * @return WordDocElementParagraph
     */
	public function createParagraph ($style = null)
    {
        return $this->createChildElement('Paragraph', $style);
    }
    
    /**
     * Create a new table in this section
     *
     * @param $style mixed
     * @return WordDocElementTable
     */
    public function createTable ($style = null)
    {
        return $this->createChildElement('Table', $style);
    }
    
    /**
     * Create a new subsection in this section
     *
     * @return WordDocElementSubSection
     */
    public function createSubSection ()
    {
        return $this->createChildElement('SubSection');
    }
    
    /**
     * Create a new list in this section
     *
     * @param $style mixed
     * @return WordDocElementList
     */
    public function createList ($listDef, $style = null)
    {
        return $this->_document->createList($listDef, $style, $this);
    }
}


?>