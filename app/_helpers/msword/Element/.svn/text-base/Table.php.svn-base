<?php

/**
 * A Table element in WordML.
 * 
 * @schema http://schemas.microsoft.com/office/word/2003/wordml
 * @element tbl
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementTable extends WordDocElement
{   
    /**
     * The number of rows in the current row span
     *
     * @var integer
     */ 
    protected $_currentRowspans = array();
    
    /**
     * The first row of the table
     *
     * @var WordDocElementTableRow
     */ 
    protected $_firstRow;
    
    /**
     * The column widths if set
     *
     * @var array
     */ 
    protected $_columns;
    
    /**
     * The property of the table.  You only need one.
     *
     * @var WordDocElementTableProperty
     */ 
    protected $_property;
    
	public function __construct ($parent, $style = null)
    {
        parent::__construct($parent, 'w:tbl');
        $this->_currentRowspans = array();
        $this->_property = $this->createChildElement('TableProperty');
        
        if ($style)
        {
            $this->_property->setStyle($style);
        }
    }
    
    /**
     * Create a new row in the table.
     *
     * @param mixed $style
     * @return WordDocElementTableRow
     */
    public function createRow ($style = null)
    {
        $row = $this->createChildElement('TableRow', $style);
        
        if (!$this->_firstRow) $this->_firstRow = $row;
        
        if ($this->_currentRowspans)
        {
            foreach ($this->_currentRowspans as &$rowspan)
            {
                if ($rowspan) $rowspan--;
            }
        }
        
        return $row;
    }
    
    /**
     * Set the width of the columns.
     * 
     * @params float ...
     * @return self
     */
    public function setColumns ()
    {
        $columns = func_get_args();
        
        foreach ($columns as $column)
        {
            $this->_columns[] = floor(WordDocDocument::TWIPS_PER_INCH * $column);
        }
        
        return $this;
    }
    
    /**
     * The number of rows that a column in this row spans.
     *
     * @param integer $column
     * @param integer $rowspan
     * @return self
     */
    public function setCurrentRowspan ($column, $rowspan)
    {
        $this->_currentRowspans[$column] = $rowspan;
        return $this;
    }
    
    /**
     * Determine if vertical cell mergin should continue for a column.
     *
     * @param integer $column
     * @return string
     */
    public function getRowspanStatus ($column)
    {
        $status = '';
        
        if (isset($this->_currentRowspans[$column]) && !empty($this->_currentRowspans[$column]))
        {
            if ($this->_currentRowspans[$column] == 1)
            {
                $status = 'end';
            }
            else
            {
                $status = 'continue';
            }
        }
        
        return $status;
    }
    
    /**
     * Set the border of the table.
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
     * Set the margins of cells in this table.
     *
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @param float $left
     * @return self
     */
    public function setCellMargins ($top, $right = null, $bottom = null, $left = null)
    {
        $this->_property->setCellMargins($top, $right, $bottom, $left);
        return $this;
    }
    
    public function contributeToWordDoc($parent, $insert = 'append')
    {
        if ($this->_firstRow)
        {
            $tblGrid = array('w:gridCol' => array());
            
            if ($this->_columns)
            {
                $numColumns = count($this->_columns);
                
                for ($i = 0; $i < $numColumns; $i++)
                {
                    $tblGrid['w:gridCol'][] = array('#attrs' => array('w:w' => $this->_columns[$i]));
                }
            }
            else
            {
                $numColumns = $this->_firstRow->getNumCells();
                
                for ($i = 0; $i < $numColumns; $i++)
                {
                    $tblGrid['w:gridCol'][] = '';
                }
            }
            
            $wTbl = array($this->_name => array('w:tblGrid' => $tblGrid));
            $table = WordDocDomUtils::AppendArrayToXML($parent, $wTbl);
            $this->childrenContributeToWordDoc($table, 'prepend');
        }
    }
}


?>