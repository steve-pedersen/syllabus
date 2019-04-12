<?php

/**
 * A cell property element in a WordML 2003 document.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementTableCellProperty extends WordDocElement
{
	/**
     * The properties of the paragraph property element
     *
     * @var array
     */ 
    protected $_properties;
    
    public function __construct ($parent)
    {
        parent::__construct($parent, 'w:tcPr');
        $this->_properties = array();
    }
    
    /**
     * Set the style of the cell property.
     * 
     * @param mixed $style
     * @return self
     */
	public function setStyle ($style)
    {
        if (is_string($style))
        {
            $style = $this->_document->getStyle($style);
        }
        
        if ($style->getTableCellProperty()->hasChildren())
        {
            $this->addChildren($style->getTableCellProperty()->getChildren());
        }
        
        return $this;
    }
    
    /**
     * Set the grid of cells with this cell property.
     *
     * @param integer $span
     * @return self
     */
    public function setGridSpan ($span)
    {
        return $this->createChildElement('Simple', 'w:gridSpan')
                ->setAttribute('w:val', $span)
                ->end();
    }
    
    /**
     * Set the width of cells with this cell property.
     *
     * @param float $width
     * @param boolean $percent
     * @return self
     */
    public function setWidth ($width, $percent = false)
    {
        $type = 'dxa';
        
        if ($percent)
        {
            $width *= 50;
            $type = 'pct';
        }
        else
        {
            $width *= WordDocDocument::TWIPS_PER_INCH;
        }
        $width = floor($width);
        return $this->createChildElement('Simple', 'w:tcW')
                ->setAttribute('w:w', $width)
                ->setAttribute('w:type', $type)
                ->end();
    }
    
    /**
     * Set the margins of cells with this cell property.
     *
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @param float $left
     * @return self
     */
    public function setMargins ($top, $right = null, $bottom = null, $left = null)
    {
        $top *= WordDocDocument::TWIPS_PER_INCH;
        $right = ($right ===  null ? $top : $right * WordDocDocument::TWIPS_PER_INCH);
        $bottom = ($bottom === null ? $top : $bottom * WordDocDocument::TWIPS_PER_INCH);
        $left = ($left === null ? $right : $left * WordDocDocument::TWIPS_PER_INCH);
        
        $this->createChildElement('Simple', 'w:tcMar')
                 ->createChildElement('Simple', 'w:top')
                    ->setAttribute('w:w', $top)
                    ->setAttribute('w:type', 'dxa')
                 ->end()
                 ->createChildElement('Simple', 'w:right')
                    ->setAttribute('w:w', $right)
                    ->setAttribute('w:type', 'dxa')
                 ->end()
                 ->createChildElement('Simple', 'w:bottom')
                    ->setAttribute('w:w', $bottom)
                    ->setAttribute('w:type', 'dxa')
                 ->end()
                 ->createChildElement('Simple', 'w:left')
                    ->setAttribute('w:w', $left)
                    ->setAttribute('w:type', 'dxa')
                 ->end()
             ->end();
        
        return $this;
    }
    
    /**
     * Set the border of cells with this cell property.
     *
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @param float $left
     * @return self
     */
    public function setBorders ($top, $right = null, $bottom = null, $left = null)
    {
        $right = ($right ===  null ? $top : $right);
        $bottom = ($bottom === null ? $top : $bottom);
        $left = ($left === null ? $right : $left);
        
        $borders = $this->createChildElement('Simple', 'w:tcBorders');
        
        if ($top)
        {
            $borders->createChildElement('Simple', 'w:top')
                    ->setAttribute('w:val', $top['style'])
                    ->setAttribute('w:color', $top['color'])
                    ->setAttribute('w:size', "0")
                    ->setAttribute('wx:bdrwidth', $top['width']);
        }
                
        if ($right)
        {
            $borders->createChildElement('Simple', 'w:right')
                    ->setAttribute('w:val', $right['style'])
                    ->setAttribute('w:color', $right['color'])
                    ->setAttribute('w:size', "0")
                    ->setAttribute('wx:bdrwidth', $right['width']);
        }
        
        if ($bottom)
        {
            $borders->createChildElement('Simple', 'w:bottom')
                    ->setAttribute('w:val', $bottom['style'])
                    ->setAttribute('w:color', $bottom['color'])
                    ->setAttribute('w:size', "0")
                    ->setAttribute('wx:bdrwidth', $bottom['width']);
        }
        
        if ($left)
        {
            $borders->createChildElement('Simple', 'w:left')
                    ->setAttribute('w:val', $left['style'])
                    ->setAttribute('w:color', $left['color'])
                    ->setAttribute('w:size', "0")
                    ->setAttribute('wx:bdrwidth', $left['width']);
        }
        return $this;
    }
    
    /**
     * Set the shading for the table cell.
     *
     * @param string $background - The background color as a hex value. 
     * @param string $foreground - The foreground color as a hex value. 
     * @param string $value - The style of shading. 
     */
    public function setShading($background, $foreground = 'auto', 
        $value = WordDocElementStyle::SHADING_STYLE_CLEAR)
    {
        $this->createChildElement('Simple', 'w:shd')
            ->setAttribute('w:val', $value)
            ->setAttribute('w:color', $foreground)
            ->setAttribute('w:fill', $background);
        return $this;
    }
}


?>