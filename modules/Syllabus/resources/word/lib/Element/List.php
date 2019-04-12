<?php

/**
 * A list is an element that does not really exist in a WordML
 * document, but is used to maintain a connection between different
 * WordML elements, namely paragraphs and the list definitions they
 * are using to display as though they were part of a list.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementList extends WordDocElement
{
	/**
	 * The list definition for the list.
	 *
	 * @var WordDocElementListDefinition
	 */ 
	protected $_listId;
    
    /**
     * The level of the list
     *
     * @var integer
     */ 
    protected $_level = 0;
    
    public function __construct ($parent, $listId, $style = null)
    {
        parent::__construct($parent, '');
        $this->_listId = $listId;
        
        if ($parent instanceof self)
        {
            $this->_level = $parent->getNextLevel();
        }
    }
    
    /**
     * Create a list within this list.  If a list definition
     * is not specified, we will have the new list be at the
     * list level below this one and still be assicated with this list's
     * list definition.
     *
     * @param WordDocElementListDefinition $listDef
     * @param mixed $style
     * @return WordDocElementList
     */
    public function createList ($listDef = null, $style = null)
    {
        $list = null;
        
        if ($listDef)
		{
			$list = $this->_document->createList($listDef, $style, $this);
		}
		else
		{
			$list = $this->createChildElement('List', "{$this->_listId}", $style);
		}
        
        return $list;
    }
    
    /**
     * Create a paragraph and associate it with this list's definition.
     *
     * @param mixed $style
     * @return WordDocElementParagraph
     */
    public function createParagraph ($style = null)
    {
        $paragraph = $this->createChildElement('Paragraph', $style);
        $paragraph->setListInformation($this->_listId, $this->_level);
        return $paragraph;
    }
    
    /**
     * Get the level below this one in the list nesting hierarchy.
     *
     * @return integer
     */
    public function getNextLevel ()
    {
        return $this->_level + 1;
    }
    
    public function contributeToWordDoc ($parent, $insertType = 'append')
    {
        $this->childrenContributeToWordDoc($parent, $insertType);
    }
}


?>