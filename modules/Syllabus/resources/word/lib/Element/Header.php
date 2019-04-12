<?php

/**
 * A header element in a WordML document.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementHeader extends WordDocElement
{
    
	public function __construct ($parent, $type)
    {
        parent::__construct($parent, 'w:hdr');
        $this->setAttribute('w:type', $type);
    }
    
    /** 
     * Create a paragraph and add it to the footer
     *
     * @param mixed $style
     * @return WordDocElementParagraph
     */
    public function createParagraph($style = null)
    {
        return $this->createChildElement('Paragraph', $style);
    }
}


?>