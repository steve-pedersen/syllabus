<?php

/**
 * A paragraph property element for a WordML document.
 * Properties specified here relate to the structture of a paragraph
 * rather than the contents.  It deals with alignment, borders, indentation,
 * and spacing.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementParagraphProperty extends WordDocElement
{   
    /**
     * The tabs stop element
     *
     * @var WordDocElement
     */ 
    protected $_tabs;
    
    public function __construct ($parent)
    {
        parent::__construct($parent, 'w:pPr');
    }
    
    /**
     * Set the style for this property to use.
     *
     * @param mixed $style
     * @return self
     */
	public function setStyle ($style)
    {
        if (is_string($style))
        {
            $this->createChildElement('Simple', 'w:pStyle')
                ->setAttribute('w:val', $style);
        }
        elseif ($style->getParagraphProperty()->hasChildren())
        {
            $this->addChildren($style->getParagraphProperty()->getChildren());
        }
        
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
    public function setListInformation ($listId, $listLevel)
    {
        $this->createChildElement('ListProperty', $listId, $listLevel);
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
        $this->createChildElement('Simple', 'w:jc')
            ->setAttribute('w:val', $alignment);
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
        $before = floor(WordDocDocument::TWIPS_PER_INCH * $before);
        $after = floor(WordDocDocument::TWIPS_PER_INCH * $after);

        $this->createChildElement('Simple', 'w:spacing')
            ->setAttribute('w:before', $before)
            ->setAttribute('w:after', $after);
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
        if (is_array($width))
        {
            $indent = $this->createChildElement('Simple', 'w:ind');
            foreach ($width as $type => $value)
            {
                $value *= WordDocDocument::TWIPS_PER_INCH;
                $value = floor($value);
                $indent->setAttribute('w:' . $type, $value);
            }
        }
        else
        {
            $width *= WordDocDocument::TWIPS_PER_INCH;
            $width = floor($width);
            $this->createChildElement('Simple', 'w:ind')
                ->setAttribute('w:' . $type, $width);
        }
        return $this;
    }
    
    /**
     * Set the borders of the paragraph.
     *
     * @param array $top - The style, color, and width of the top border.
     * @param array $right - The style, color, and width of the right border.
     * @param array $bottom - The style, color, and width of the bottom border.
     * @param array $left - The style, color, and width of the left border.
     * @return self
     */
    public function setBorders ($top, $right = null, $bottom = null, $left = null)
    {
        $right = ($right ===  null ? $top : $right);
        $bottom = ($bottom === null ? $top : $bottom);
        $left = ($left === null ? $right : $left);
        
        $borders = $this->createChildElement('Simple', 'w:pBdr');
        
        if ($top)
        {
            $borders->createChildElement('Simple', 'w:top')
                    ->setAttribute('w:val', $top['style'])
                    ->setAttribute('w:color', $top['color'])
                    ->setAttribute('w:size', $top['width']);
        }
                
        if ($right)
        {
            $borders->createChildElement('Simple', 'w:right')
                    ->setAttribute('w:val', $right['style'])
                    ->setAttribute('w:color', $right['color'])
                    ->setAttribute('w:size', $right['width']);
        }
        
        if ($bottom)
        {
            $borders->createChildElement('Simple', 'w:bottom')
                    ->setAttribute('w:val', $bottom['style'])
                    ->setAttribute('w:color', $bottom['color'])
                    ->setAttribute('w:size', $bottom['width']);
        }
        
        if ($left)
        {
            $borders->createChildElement('Simple', 'w:left')
                    ->setAttribute('w:val', $left['style'])
                    ->setAttribute('w:color', $left['color'])
                    ->setAttribute('w:size', $left['width']);
        }
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
        $position = floor($position * WordDocDocument::TWIPS_PER_INCH);
        
        if (!$this->_tabs)
        {
            $this->_tabs = $this->createChildElement('Simple', 'w:tabs');
        }
        
        $this->_tabs->createChildElement('Simple', 'w:tab')
            ->setAttribute('w:val', $type)
            ->setAttribute('w:pos', $position);
        
        return $this;
    }
    
    /**
     * Set the shading for the paragraph.
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
        $property = WordDocDomUtils::AppendArrayToXML($parent, array('w:pPr' => ''));
        $this->childrenContributeToWordDoc($property);
    }
}


?>