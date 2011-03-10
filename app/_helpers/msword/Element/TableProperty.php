<?php

/**
 * A table property element in a WordML 2003 document.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementTableProperty extends WordDocElement
{
	/**
     * The properties of the paragraph property element
     *
     * @var array
     */ 
    protected $_properties;
    
    public function __construct ($parent)
    {
        parent::__construct($parent, 'w:tblPr');
        $this->_properties = array();
    }
    
	public function setStyle ($style)
    {
        if (is_string($style))
        {
            $styleProperty = $this->createChildElement('Simple', 'w:tblStyle');
            $styleProperty->setAttribute('w:val', $style);
        }
		else
		{
			if ($properties = $style->getProperties('table'))
			{
				$this->_properties += $properties;
			}
		}
        
        return $this;
    }
    
    public function setWidth ($width, $percent = false)
    {
        $type = 'dxa';
        
        if ($percent)
        {
            $width /= 50;
            $type = 'pct';
        }
        else
        {
            $width *= WordDocDocument::TWIPS_PER_INCH;
        }
        return $this->createChildElement('Simple', 'w:tcW')
                ->setAttribute('w:w', $width)
                ->setAttribute('w:type', $type)
                ->end();
    }
    
    /**
     * Set the margins of cells in tables with this cell property.
     *
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @param float $left
     * @return self
     */
    public function setCellMargins ($top, $right = null, $bottom = null, $left = null)
    {
        $top *= WordDocDocument::TWIPS_PER_INCH;
        $right = ($right ===  null ? $top : $right * WordDocDocument::TWIPS_PER_INCH);
        $bottom = ($bottom === null ? $top : $bottom * WordDocDocument::TWIPS_PER_INCH);
        $left = ($left === null ? $right : $left * WordDocDocument::TWIPS_PER_INCH);
        
        $this->createChildElement('Simple', 'w:tblCellMar')
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
     * Set the borders of the tables with this table property.
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
        
        $borders = $this->createChildElement('Simple', 'w:tblBorders');
        
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
     * Set the shading for the cell spaing gaps in a table.
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
    
    public function contributeToWordDoc($parent, $insert = 'append')
    {
        $property = WordDocDomUtils::AppendArrayToXML($parent, array($this->_name => $this->_properties));
        $this->childrenContributeToWordDoc($property, 'prepend');
    }
}


?>