<?php

/**
 * A table row element in a WordML 2003 document.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementTableRow extends WordDocElement
{
    /**
     * The number of cells in this row.
     *
     * @var integer
     */ 
    protected $_numCells = 0;
    
    /**
     * The property for the row
     *
     * @var 
     */ 
    protected $_property;
    
    public function __construct($parent)
    {
        parent::__construct($parent, 'w:tr');
        $this->_property = $this->createChildElement('TableRowProperty');
    }
    
    /**
     * Retrieve the table that this row is contained in.
     *
     * @return WordDocElementTable
     */
	public function getTable ()
    {
        return $this->_parent;
    }
    
    /**
     * Retrieve the number of cells in this row
     *
     * @return integer
     */
    public function getNumCells()
    {
        return $this->_numCells;
    }
    
    /**
     * Create a cell in this row.
     *
     * @param mixed $style
     * @return self
     */
    public function createCell ($style = null)
    {
        $cell = $this->createChildElement('TableCell', $style);
        
        if ($rowspan = $this->getTable()->getRowspanStatus($this->_numCells))
        {
            $cell->setVMerge($rowspan);
        }
        
        $this->_numCells++;
        
        return $cell;
    }
    
    /**
     * Set the height of the row.
     *
     * @param float $height
     * @param string $type - values to determine how to use the height value.
     *  1. Auto size the row: 'auto'
     *  2. Fixed height: 'exact'
     *  3. Minimum height: 'at-least'
     * @return self
     */
    public function setHeight ($height, $type = 'exact')
    {
        $height *= WordDocDocument::TWIPS_PER_INCH;
        $this->_property->createChildElement('Simple', 'w:trHeight')
            ->setAttribute('w:val', $height)
            ->setAttribute('w:h-rule', $type);
        
        return $this;
    }
    
    /**
     * Set the alignment of the row
     *
     * @parm string $alignment
     * @return self
     */
    public function setAlignment ($alignment)
    {
        $this->_property->createChildElement('Simple', 'w:jc')
            ->setAttribute('w:val', $alignment);
        return $this;
    }
}


?>