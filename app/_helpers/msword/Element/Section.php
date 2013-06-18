<?php

/**
 * A section element in WordML
 * 
 * @schema http://schemas.microsoft.com/office/word/2003/auxHint
 * @element sect
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementSection extends WordDocElement
{
    public function __construct ($parent)
    {
        parent::__construct($parent, 'wx:sect');
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
     * Create a new run inside a new paragraph in this section.
     *
     * @param $style mixed
     * @return WordDocElementRun
     */
    public function createRun ($style = null)
    {
        return $this->createParagraph()->createRun($style);
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
     * Create a new section property for this section
     *
     * @return WordDocElementSectionProperty
     */
    public function createSectionProperty ()
    {
        return $this->createChildElement('SectionProperty');
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