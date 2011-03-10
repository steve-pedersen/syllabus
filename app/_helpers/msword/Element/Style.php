<?php
	
/**
 * A style element for a WordML document.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementStyle extends WordDocElement 
{
    const STYLE_TYPE_CHARACTER = 'character';
    const STYLE_TYPE_PARAGRAPH = 'paragraph';
    const STYLE_TYPE_TABLE = 'table';
    const ALIGN_CENTER = 'center';
    const ALIGN_LEFT = 'left';
    const ALIGN_RIGHT = 'right';
    const ALIGN_TOP = 'top';
    const ALIGN_BOTTOM = 'bottom';
    
    const TAB_JUSTIFICATION_CLEAR = 'clear';
    const TAB_JUSTIFICATION_LEFT = 'left';
    const TAB_JUSTIFICATION_RIGHT = 'right';
    const TAB_JUSTIFICATION_CENTER = 'center';
    const TAB_JUSTIFICATION_DECIMAL = 'decimal';
    const TAB_JUSTIFICATION_BAR = 'bar';
    const TAB_JUSTIFICATION_LIST = 'list';
    
 	

const SHADING_STYLE_NIL = 'nil';
const SHADING_STYLE_CLEAR = 'clear';
const SHADING_STYLE_SOLID = 'solid';
const SHADING_STYLE_HORIZONTAL_STRIPE = 'horz-stripe';
const SHADING_STYLE_VERTICAL_STRIPE = 'vert-stripe';
const SHADING_STYLE_REVERSE_DIAGONAL_STRIPE = 'reverse-diag-stripe';
const SHADING_STYLE_DIAGONAL_STRIPE = 'diag-stripe';
const SHADING_STYLE_HORIZONTAL_CROSS = 'horz-cross';
const SHADING_STYLE_DIAGONAL_CROSS = 'diag-cross';
const SHADING_STYLE_THIN_HORIZONTAL_STRIPE = 'thin-horz-stripe';
const SHADING_STYLE_THIN_VERTICAL_STRIPE = 'thin-vert-stripe';
const SHADING_STYLE_THIN_REVERSE_DIAGONAL_STRIPE = 'thin-reverse-diag-stripe';
const SHADING_STYLE_THIN_DIAGONAL_STRIPE = 'thin-diag-stripe';
const SHADING_STYLE_THIN_HORIZONTAL_CROSS = 'thin-horz-cross';
const SHADING_STYLE_THIN_DIAGONAL_CROSS = 'thin-diag-cross';
const SHADING_STYLE_PCT_5 = 'pct-5';
const SHADING_STYLE_PCT_10 = 'pct-10';
const SHADING_STYLE_PCT_12 = 'pct-12';
const SHADING_STYLE_PCT_15 = 'pct-15';
const SHADING_STYLE_PCT_20 = 'pct-20';
const SHADING_STYLE_PCT_25 = 'pct-25';
const SHADING_STYLE_PCT_30 = 'pct-30';
const SHADING_STYLE_PCT_35 = 'pct-35';
const SHADING_STYLE_PCT_37 = 'pct-37';
const SHADING_STYLE_PCT_40 = 'pct-40';
const SHADING_STYLE_PCT_45 = 'pct-45';
const SHADING_STYLE_PCT_50 = 'pct-50';
const SHADING_STYLE_PCT_55 = 'pct-55';
const SHADING_STYLE_PCT_60 = 'pct-60';
const SHADING_STYLE_PCT_62 = 'pct-62';
const SHADING_STYLE_PCT_65 = 'pct-65';
const SHADING_STYLE_PCT_70 = 'pct-70';
const SHADING_STYLE_PCT_75 = 'pct-75';
const SHADING_STYLE_PCT_80 = 'pct-80';
const SHADING_STYLE_PCT_85 = 'pct-85';
const SHADING_STYLE_PCT_87 = 'pct-87';
const SHADING_STYLE_PCT_90 = 'pct-90';
const SHADING_STYLE_PCT_95 = 'pct-95';   
    /**
     * The id of the style.
     *
     * @var string
     */ 
    protected $_styleId;
    
    /**
     * Paragraph properties for the style
     *
     * @var WordDocElementParagraphProperty
     */ 
    protected $_pPr;
    
    /**
     * Run properties for the style
     *
     * @var WordDocElementRunProperty
     */ 
    protected $_rPr;
    
    /**
     * Table properties for the style
     *
     * @var WordDocElementTableProperty
     */ 
    protected $_tblPr;
    
    /**
     * Table row properties for the style
     *
     * @var WordDocElementTableRowProperty
     */ 
    protected $_trPr;
    
    /**
     * Table cell properties for the style
     *
     * @var WordDocElementTableCellProperty
     */ 
    protected $_tcPr;
    
    public function __construct($parent, $id, $name = null, $default = false)
    {
        parent::__construct($parent, 'w:style');
        $this->_styleId = $id;
        
        $this->setAttribute('w:styleId', $id);
        
        if ($default)
        {
            $this->setAttribute('w:default', 'on');
        }
        
        if ($name)
        {
            $this->createChildElement('Simple', 'w:name')
                ->setAttribute('w:val', $name);
        }
    }

	/**
	 * Determine if the style has been added to the styles section of the document.
	 *
	 * @return bool
	 **/
	public function isDocumentedStyle ()
	{
		return $this->_document->getStyle($this->_styleId);
	}
    
    /**
     * Return the single instance of a paragraph property for this style.
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
     * Return the single instance of a run property for this style.
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
    
    /**
     * Return the single instance of a table property for this style.
     *
     * @return WordDocElementTableProperty
     */
    public function getTableProperty ()
    {
        if (!$this->_tblPr)
        {
            $this->_tblPr = $this->createChildElement('TableProperty');
        }
        
        return $this->_tblPr;
    }
    
    /**
     * Return the single instance of a table row property for this style.
     *
     * @return WordDocElementTableRowProperty
     */
    public function getTableRowProperty ()
    {
        if (!$this->_trPr)
        {
            $this->_trPr = $this->createChildElement('TableRowProperty');
        }
        
        return $this->_trPr;
    }
    
    /**
     * Return the single instance of a table cell property for this style.
     *
     * @return WordDocElementTableCellProperty
     */
    public function getTableCellProperty ()
    {
        if (!$this->_tcPr)
        {
            $this->_tcPr = $this->createChildElement('TableCellProperty');
        }
        
        return $this->_tcPr;
    }

    
	/**
	 * Return the style id for this style
	 *
	 * @return string
	 **/
	public function getStyleId ()
	{
		return $this->_styleId;
	}
    
    /**
     * Set the id of the style this style will be based on.
     *
     * @param string $id
     * @return self
     */
    public function setBasedOn ($id)
    {
        $this->createChildElement('Simple', 'w:basedOn')
            ->setAttribute('w:val', $id);
        return $this;
    }
    
    /**
     * Set the type of content this style should apply to.
     *
     * @param string $styleType - The particular values are class constants
     * @return self
     */
    public function applyTo ($styleType)
    {
        $this->setAttribute('w:type', $styleType);
        return $this;
    }
    
    /**
     * Set what the next style sghould be if someone were to sedit the document
     * and hit enter at a point where this style is active.
     *
     * @param string $id
     * @return self
     */
    public function setNextStyle ($id)
    {
        $this->createChildElement('Simple', 'w:next')
            ->setAttribute('w:val', $id);
        return $this;
    }
}
    
?>