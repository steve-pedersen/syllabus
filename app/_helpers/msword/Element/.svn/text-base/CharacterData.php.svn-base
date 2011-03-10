<?php

/**
 * Character data in a word doc.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementCharacterData extends WordDocElement
{
	/**
	 * The text to write
	 *
	 * @var string
	 */ 
	protected $_text;
    
    public function __construct ($parent, $text)
    {
        parent::__construct($parent, '#cdata');
        $this->_text = $text;
    }
    
    /**
     * Contribute XML to the Word document by inserting XML into the parent
     *
     * @param DOMNode $parent
     * @param string $insertType - The values can be: 'append' or 'prepend'
     */
	public function contributeToWordDoc ($parent, $insertType = 'append')
    {
        WordDocDomUtils::AppendArrayToXML($parent, array($this->_name => $this->_text));
    }
}


?>