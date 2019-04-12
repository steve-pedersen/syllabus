<?php

/**
 * A list definition element in a WordML document.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementListDefinition extends WordDocElement
{
    /**
     * The id for the list definition.
     *
     * @var string
     */ 
    protected $_id;
    
    /**
     * The next level to be created
     *
     * @var integer
     */ 
    protected $_nextLevel = 0;
    
	public function __construct ($parent, $type, $id)
    {
        parent::__construct($parent, 'w:listDef');
        $this->_id = $id;
        $this->createChildElement('Simple', 'w:plt')->setAttribute('w:val', $type);
        $this->setAttribute('w:listDefId', $this->_id);
    }
    
    /**
     * Create a new level for this list definition.
     * 
     * @param mixed $style
     * @return WordDocElementListLevel
     */
    public function createLevel ($style = null)
    {
        return $this->createChildElement('ListLevel', $this->_nextLevel++, $style);
    }
    
    /**
     * Get the id of this list definition.
     *
     * @return string
     */
    public function getId ()
    {
        return $this->_id;
    }
    
    /**
     * Set the id of the list definition.
     *
     * @param string $id
     * @return self
     */
    public function setId ($id)
    {
        $this->_id = $id;
        $this->setAttribute('w:listDefId', $this->_id);
        return $this;
    }
}


?>