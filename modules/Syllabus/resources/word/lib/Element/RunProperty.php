<?php

/**
 * A run property lement for a WordML 2003 document
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementRunProperty extends WordDocElement
{
    public function __construct ($parent)
    {
        parent::__construct($parent, 'w:rPr');
    }
    
    /**
     * set the styles for this run property
     *
     * @param mixed $style
     * @return self
     */
	public function setStyle ($style)
    {
        if (is_string($style))
        {
            $this->createChildElement('Simple', 'w:rStyle')
                ->setAttribute('w:val', $style);
        }
		elseif ($style->getRunProperty()->hasChildren())
        {
            $this->addChildren($style->getRunProperty()->getChildren());
        }
        
        return $this;
    }
    
    /**
     * Have this run of text use superscript.
     *
     * @return self
     */
	public function setSuperscript ()
	{
        $this->createChildElement('Simple', 'w:vertAlign')
            ->setAttribute('w:val', 'superscript');
        return $this;
	}
    
    /**
     * Have this run of text use subscript.
     *
     * @return self
     */
	public function setSubscript ()
	{
        $this->createChildElement('Simple', 'w:vertAlign')
            ->setAttribute('w:val', 'subscript');
        return $this;
	}
    
    /**
     * Have this run of text use underline.
     *
     * @return self
     */
    public function setUnderline ($type = 'single')
    {
        $this->createChildElement('Simple', 'w:u')
            ->setAttribute('w:val', $type);
        return $this;
    }
    
    /**
     * Have this run of text use bold.
     *
     * @return self
     */
    public function setBold ()
    {
       $this->createChildElement('Simple', 'w:b')
            ->setAttribute('w:val', 'on');
        return $this;
    }
    
    /**
     * Have this run of text use italics.
     *
     * @return self
     */
    public function setItalics ()
    {
        $this->createChildElement('Simple', 'w:i')
            ->setAttribute('w:val', 'on');
        return $this;
    }
    
    /**
	 * Set the font name for the style
	 *
	 * @param string $ascii - The font to use for ASCII and the default font for the rest of the fonts
	 * @param string $hAnsi - the font for high ANSI
	 * @param string $cs - the font for complex script
	 * @param string $fareast - the font for fareast
	 * @param string $hint - Gets or sets a hint to Word as to which font to use for display (default, fareast, cs).
	 *
	 * @return DocWordElementStyle
	 **/
	public function setFontName ($ascii, $hAnsi = null, $cs = null, $fareast = null, $hint = null)
	{
		$hAnsi = ($hAnsi ? $hAnsi : $ascii);
		$cs = ($cs ? $cs : $ascii);
		$fareast = ($fareast ? $fareast : $ascii);
		$hint = ($hint ? $hint : 'default');
        
        $this->createChildElement('Simple', 'w:rFonts')
            ->setAttribute('w:ascii', $ascii)
            ->setAttribute('w:h-ansi', $hAnsi)
            ->setAttribute('w:cs', $cs)
            ->setAttribute('w:fareast', $fareast)
            ->setAttribute('w:hint', $hint);
            
        
		return $this;
	}
	
	/**
	 * Set the font size.
	 *
	 * @return self
	 **/
	public function setFontSize ($size)
	{
		$size *= 2;
        $this->createChildElement('Simple', 'w:sz')
            ->setAttribute('w:val', $size);
		
		return $this;
	}
	
	/**
	 * Set the color of the font.
	 *
	 * @return self
	 **/
	public function setFontColor ($color)
	{
		if ($this->validateColor($color, false))
		{
            $this->createChildElement('Simple', 'w:color')
                ->setAttribute('w:val', $color);
		}
		
		return $this;
	}
    
    /**
     * Set the shading for the characters in the run.
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
    
    protected function validateColor ($color, $throwException = true)
    {
        $result = ($color == 'auto' || preg_match('/^[0-9A-F]{6}$/', $color));
        
        if (!$result && $throwException) 
            throw new Exception('Invalid color set: ' . $color);
        
        return $result;
    }
}


?>