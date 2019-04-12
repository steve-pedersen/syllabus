<?php

/**
 * A representation of a Paragraph element in WordML
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementParagraph extends WordDocElement
{
    public function __construct ($parent, $style = null)
    {
        parent::__construct($parent);
        
        if ($style)
        {
            $property = $this->createChildElement('ParagraphProperty');
            $property->setStyle($style);
        }
    }

    /**
     * Create a hyper link.
     * 
     * @param mixed $style
     * @return WordDocElementHyperLink
     */
	public function createHyperLink ($style = null)
	{
		$this->_children[] = $hyperLink = WordDocElement::GetNewElement('HyperLink', $this, $style);
        return $hyperLink;
	}
    
    /**
     * Create a run.
     * 
     * @param mixed $style
     * @return WordDocElementRun
     */
	public function createRun ($style = null)
    {
        $run = $this->createChildElement('Run', $style);
        return $run;
    }
    
    /**
	 * Add text to the paragraph.
	 *
     * @param string $text
	 * @return self
	 */
	public function addText ($text)
	{
		return $this->createRun()->addText($text)->end();
	}
    
    /**
	 * Add a break.
	 *
     * @param string $text
	 * @return self
	 */
	public function addBreak ()
	{
		return $this->createRun()->addBreak()->end();
	}
    
    /**
	 * Add HTML to the paragraph.
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
     * Add an available tab stop to the paragraph.  This tab stop does not
     * appear in the content, but rather defines how a tab should behave 
     * in this paragraph.
     *
     * @param float $width
     * @param string $type
     * @return self
     */
    public function addTabStop ($position, $type = 'left')
    {
        $property = $this->createChildElement('ParagraphProperty');
        $property->addTabStop($position, $type);
        return $this;
    }
    
    /**
     * Create a new paragraph property and add new styles to it.
     *
     * @param mixed $style
     * @return self
     */
    public function changeStyle ($style)
    {
        $property = $this->createChildElement('ParagraphProperty');
        $property->setStyle($style);
        return $this;
    }
    
    /**
     * If this paragraph is part of a list in the document, then set the id 
     * of the list and the level this paragraph belongs to.
     *
     * @param integer $listId
     * @param integer $listLevel
     * @return self
     */
    public function setListInformation ($listId, $listlevel)
    {
        $this->createChildElement('ParagraphProperty')->setListInformation($listId, $listlevel);
        return $this;
    }
    
    /**
     * Set the alignment of the paragraph.
     *
     * @param string $alignment - alignments are specified in the 
     *      WordDocElementStyle class as constants.
     * @return self
     */
    public function setAlignment ($alignment)
    {
        $this->createChildElement('ParagraphProperty')->setAlignment($alignment);
        return $this;
    }
    
    /**
     * Set the spacing for the lines before and after the paragraph.
     *
     * @param integer $before
     * @param integer $after
     * @return self
     */
    public function setSpacing ($before = 0, $after = 0)
    {
        $this->createChildElement('ParagraphProperty')->setSpacing($before, $after);
        return $this;
    }
    
    /**
     * Set the indentation for the paragraph.
     *
     * @param mixed $width - If this is an integer, this value will be
     *      applied to whatever $type is specified (or 'left' if not specified).
     *      Otherwise this should be an associative array with the keys as the 
     *      indentation types and the values as the width.
     * @param integer $type - Only used if $width is an integer
     * @return self
     */
    public function setIndent ($width, $type = 'left')
    {
        $this->createChildElement('ParagraphProperty')->setIndent($width, $type);
        return $this;
    }
    
    /**
     * Contribute XML to the Word document by inserting XML into the parent
     *
     * @param DOMNode $parent
     * @param string $insertType - The values can be: 'append' or 'prepend'
     */
	public function contributeToWordDoc ($parent, $insertType = 'append')
    {
        $paragraph = WordDocDomUtils::AppendArrayToXML($parent, array('w:p' => ''));
        $this->childrenContributeToWordDoc($paragraph);
    }
}


?>