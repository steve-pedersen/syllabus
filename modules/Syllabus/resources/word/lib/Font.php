<?php

/**
 * A font element in a word document.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
abstract class WordDocFont implements WordDocIContributor
{
    /**
     * The name of the font.
     *
     * @var string
     */ 
    protected $_name;
    
    /**
     * The alternative name of the font.
     *
     * @var string
     */ 
    protected $_altName;
    
    /**
     * The PANOSE typeface classification number.
     *
     * @var string
     */ 
    protected $_panose1;
    
    /**
     * The characters set for the font.
     *
     * @var string
     */ 
    protected $_charset;
    
    /**
     * The font family the font belongs to.
     *
     * @var string
     */ 
    protected $_fontfamily;
    
    /**
     * Indicates that the font is not TrueType or OpenType
     *
     * @var bool
     */
    protected $_notTrueType;
    
    /**
     * Specifies the font pitch.  The values can be:
     * 'fixed', 'variable', and 'default'
     *
     * @var string
     */ 
    protected $_pitch;
    
    /**
     * Contains information identifying the code pages and 
     * Unicode subranges for which a specified font provides glyphs.
     *
     * @var array
     */ 
    protected $_sig;
    
    /**
     * Load a font.
     * 
     * @param string $fontName
     * @return WordDocFont
     */
    public static function LoadFont ($fontName)
    {
        $font = null;
        $fontName = str_replace(' ', '_', $fontName);
        $fontPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Font' . DIRECTORY_SEPARATOR . $fontName . '.php';
        $fontClass = 'WordDocFont' . $fontName;
        
        if (file_exists($fontPath))
        {
            require_once($fontPath);
            $font = new $fontClass();
        }
        
        return $font;
    }
    
    /**
     * Return the name of the font.
     *
     * @return string
     */
    public function getName ()
    {
        return $this->_name;
    }
    
	/**
     * Contribute XML to the Word document by inserting XML into the parent
     *
     * @param DOMNode $parent
     * @param string $insertType - The values can be: 'append' or 'prepend'
     */
	public function contributeToWordDoc ($parent, $insertType = 'append')
    {
        $wFont = array('#attrs' => array('w:name' => $this->_name));
        
        if ($this->_altName)
        {
            $wFont['w:altName'] = array('#attrs' => array('w:val' => $this->_altName));
        }
        
        if ($this->_panose1)
        {
            $wFont['w:panose-1'] = array('#attrs' => array('w:val' => $this->_panose1));
        }
        
        if ($this->_charset)
        {
            $wFont['w:charset'] = array('#attrs' => array('w:val' => $this->_charset));
        }
        
        if ($this->_fontfamily)
        {
            $wFont['w:family'] = array('#attrs' => array('w:val' => $this->_fontfamily));
        }
        
        if ($this->_notTrueType)
        {
            $wFont['w:notTrueType'] = '';
        }
        
        if ($this->_pitch)
        {
            $wFont['w:pitch'] = array('#attrs' => array('w:val' => $this->_pitch));
        }
        
        if ($this->_sig)
        {
            $wFont['w:sig'] = array('#attrs' => $this->_sig);
        }
        
        WordDocDomUtils::AppendArrayToXml($parent, array('w:font' => $wFont));
    }
}


?>