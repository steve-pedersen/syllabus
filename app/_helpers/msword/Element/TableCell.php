<?php

/**
 * A table cell element in WordML
 * 
 * @schema http://schemas.microsoft.com/office/word/2003/wordml
 * @element tc
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementTableCell extends WordDocElement
{
    /**
     * The property for the cell.  There only needs to be one.
     *
     * @var integer
     */ 
    protected $_property;
    
    public function __construct ($parent, $style = null)
    {
        parent::__construct($parent, 'w:tc');
        $this->_property = $this->createChildElement('TableCellProperty');
        
        if ($style)
        {
            $this->_property->setStyle($style);
        }
    }
    
    /**
     * Retrieve the table that this cell is contained in.
     *
     * @return WordDocElementTable
     */
	public function getTable ()
    {
        return $this->_parent->getTable();
    }
    
    /**
     * Set the width of the cell.
     *
     * @param float $width
     * @return self
     */
    public function setWidth ($width)
    {
        $this->_property->setWidth($width);
        return $this;
    }
    
    /**
     * Set the margins of the cell.
     *
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @param float $left
     * @return self
     */
    public function setMargins ($top, $right = null, $bottom = null, $left = null)
    {
        $this->_property->setMargins($top, $right, $bottom, $left);
        return $this;
    }
    
    /**
     * Set the border of the cell.
     *
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @param float $left
     * @return self
     */
    public function setBorders ($top, $right = null, $bottom = null, $left = null)
    {
        $this->_property->setBorders($top, $right, $bottom, $left);
        return $this;
    }
    
    /**
     * Set the gridspan of the cell.
     *
     * @param integer $span
     * @return self
     */
    public function setGridSpan ($span)
    {
        $this->_property->setGridSpan($span);
        return $this;
    }
    
    /**
     * Set the number of columns this cell should span.
     *
     * @param integer $colspan
     * @return self
     */
    public function setColspan ($colspan)
    {
        $this->setVMerge('restart');
        
        for ($i = 2; $i < $colspan; $i++)
        {
            $cell = $this->_parent->createElement('TableCell')->setHMerge();
        }
        
        $this->_parent->createElement('TableCell')->setHMerge('end');
        
        return $this;
    }
    
    /**
     * Set the number of rows this cell should span.
     *
     * @param integer $rowspan
     * @return self
     */
    public function setRowspan ($rowspan)
    {
        $this->getTable()->setCurrentRowspan($this->_parent->getNumCells() - 1, $rowspan);
        $this->setVMerge('restart');
        return $this;
    }
    
    /**
     * Set the merge type of this cell.  The cell can:
     *  1. Start a new horizontal merge of cells: 'restart'
     *  2. Continue a horizontal merge of cells: 'continue'
     *  3. End the merging of contiguous cells: 'end'
     *
     * @param string $type
     * @return self
     */
    public function setVMerge ($type = 'continue')
    {
        $type = ($type == 'end' ? '' : $type);
        $vmerge = $this->_property->createChildElement('Simple', 'w:vmerge');
        
        if ($type)
        {
            $vmerge->setAttribute('w:val', $type);
        }
        
        return $this;
    }
    
    /**
     * Set the merge type of this cell.  The cell can:
     *  1. Start a new vertical merge of cells: 'restart'
     *  2. Continue a vertical merge of cells: 'continue'
     *  3. End the merging of vertical cells: 'end'
     *
     * @param string $type
     * @return self
     */
    public function setHMerge ($type = 'continue')
    {
        $type = ($type == 'end' ? '' : $type);
        $vmerge = $this->_property->createChildElement('Simple', 'w:hmerge');
        
        if ($type)
        {
            $vmerge->setAttribute('w:val', $type);
        }
        
        return $this;
    }
    
    /**
     * Set the vertical alignment of the contents of the cell.
     *
     * @param string $align - 'top', 'bottom', 'center', or 'both'
     * @return self
     */
    public function setVAlign ($align)
    {
        $this->_property->createChildElement('Simple', 'w:vAlign')
            ->setAttribute('w:val', $align);
        
        return $this;
    }
    
    /**
     * Add text to the cell.
     * 
     * @param string $text
     * @return self
     */
    public function addText ($text)
    {
        return $this->createParagraph()->addText($text)->end();
    }
    
    /**
     * Create a paragraph in the cell.
     *
     * @param mixed $style
     * @return self
     */
    public function createParagraph ($style = null)
    {
        return $this->createChildElement('Paragraph', $style);
    }
	
	/**
     * Create a new table in this cell
     *
     * @param $style mixed
     * @return WordDocElementTable
     */
    public function createTable ($style = null)
    {
        return $this->createChildElement('Table', $style);
    }
    
    /**
     * Create a new list in this table cell
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