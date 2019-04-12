<?php

/**
 * A list level element in a WordMl document. Each level of a list
 * is a level of nesting in the document.  This class allows you to
 * set the type of numbering or bullets, as well as the style for the text,
 * of a list level.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementListLevel extends WordDocElement
{
    /**
     * List number type. 1, 2, 3, 4, ...
     *
     * @const integer
     */
    const NFC_ARABIC = 0;
    
    /**
     * List number type. I, II, III, IV, ...
     *
     * @const integer
     */
    const NFC_UC_ROMAN = 1;
    
    /**
     * List number type. i, ii, iii, iv, ...
     *
     * @const integer
     */
    const NFC_LC_ROMAN = 2;
    
    /**
     * List number type. A, B, C, D, ...
     *
     * @const integer
     */
    const NFC_UC_LETTER = 3;
    
    /**
     * List number type. a, b, c, d, ...
     *
     * @const integer
     */
    const NFC_LC_LETTER = 4;
    
    /**
     * List number type. 1st, 2nd, 3rd, 4th, ...
     *
     * @const integer
     */
    const NFC_ORDINAL = 5;
    
    /**
     * List number type. One, Two, Three, Four, ...
     *
     * @const integer
     */
    const NFC_CARD_TEXT = 6;
    
    /**
     * List number type. First, Second, Third, Fourth, ...
     *
     * @const integer
     */
    const NFC_ORD_TEXT = 7;
    
    /**
     * List number type. Bullet
     *
     * @const integer
     */
    const NFC_BULLET = 23;

    /**
     * The level is for the level
     *
     * @var integer
     */ 
    protected $_level;
    
    /**
     * Paragraph properties for the list level
     *
     * @var WordDocElementParagraphProperty
     */ 
    protected $_pPr;
    
    /**
     * Run properties for the list level
     *
     * @var WordDocElementRunProperty
     */ 
    protected $_rPr;
    
    
    public function __construct ($parent, $level, $style = null)
    {
        parent::__construct($parent);
        $this->_level = "$level";
        
        if ($style)
        {
            if (is_string($style))
            {
                $style = $this->_document->getStyle($style);
            }
            
            if ($style->hasRunProperty())
            {
                $this->createChildElement('RunProperty')->setStyle($style);
            }
            
            if ($style->hasParagraphProperty())
            {
                $this->createChildElement('ParagraphProperty')->setStyle($style);
            }
        }
    }
    
    /**
     * Set the start number for the list.  This is independent of
     * how the numbering is represented in the document.  Starting
     * with '1' will have first element of the numbering scheme
     * appear first in the list.
     *
     * @param integer $startNumber
     * @return self
     */
    public function setStartNumber ($startNumber)
    {
        $this->createChildElement('Simple', 'w:start')
             ->setAttribute('w:val', "$startNumber");
        return $this;
    }
    
    /**
     * Set the type for the numbering scheme to use.
     *
     * @param integer $nfc - These are specified a class constants
     * @return self
     */
    public function setNumberType ($nfc)
    {
        $this->createChildElement('Simple', 'w:nfc')
             ->setAttribute('w:val', "$nfc");
        return $this;
    }
    
    /**
     * Set the text for the bullets.
     *
     * @param string $bulletText
     * @return self
     */
    public function setBulletText ($bulletText)
    {
        $this->createChildElement('Simple', 'w:lvlText')
             ->setAttribute('w:val', $bulletText);
        return $this;
    }
    
    /**
     * Set the alignment for the list.
     *
     * @param string $alignment
     * @return self
     */
    public function setAlignment ($alignment)
    {
        $this->createChildElement('Simple', 'w:lvlJc')
             ->setAttribute('w:val', $alignment);
        return $this;
    }
    
    /**
     * Return the single instance of a paragraph property for this list level.
     *
     * @return WordDocElementParagraphProperty
     */
    public function getParagraphProperty ()
    {
        if (!$this->_pPr)
        {
            $this->_pPr = $this->createChildElement('ParagraphProperty');
        }
        
        return $this->_pPr;
    }
    
    /**
     * Return the single instance of a run property for this list level.
     *
     * @return WordDocElementRunProperty
     */
    public function getRunProperty ()
    {
        if (!$this->_rPr)
        {
            $this->_rPr = $this->createChildElement('RunProperty');
        }
        
        return $this->_rPr;
    }
    
    public function contributeToWordDoc ($parent, $insertType = 'append')
    {
        $level = WordDocDomUtils::AppendArrayToXML($parent, array('w:lvl' => array('#attrs' => array('w:ilvl' => $this->_level))));
        $this->childrenContributeToWordDoc($level, $insertType);
    }
}


?>