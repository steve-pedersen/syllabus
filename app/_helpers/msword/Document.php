<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DomUtils.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'IContributor.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Font.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'HtmlParser.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Element.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Element' . DIRECTORY_SEPARATOR . 'Style.php';


	
/**
 * Word document generator.  This class is the entry point for generating
 * a WordML 2003 document.  
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocDocument 
{
    /**
     * I don't know what a twip is, but that is how many it takes
     * to make an inch in word.
     */
    const TWIPS_PER_INCH = 1440;
    
	/**
	 * The styles in the styles section of the document
	 *
	 * @var array
	 */ 
	protected $_styles;
    
    /**
     * The document properties
     *
     * @var array
     */ 
    protected $_docPr;
    
    /**
     * The fonts defined in the document
     *
     * @var array
     */ 
    protected $_fonts;
    
    /**
     * the list style definitions for the document
     *
     * @var array
     */ 
    protected $_listStyleDefs;
    
    /**
     * The body element
     *
     * @var WordDocElementBody
     */
    protected $_body;
    
    /**
     * The lists in the document
     *
     * @var array
     */ 
    protected $_lists;
    
    /**
     * The default font for the document
     *
     * @var string
     */ 
    protected $_defaultFont;
    
    /**
     * The section property for the docuement
     *
     * @var 
     */ 
    protected $_sectionPr;
    
    /**
     * A Configuration object to allow for customizations.
     *
     * Available keys are:
     *      "html-element-path-extra" - if you have a folder 
     *                  with html handlers that are added or
     *                  override default handlers.
     *      "html-element-class-prefix" - if you add new handlers
     *                  but do not want to use the standard class
     *                  prefix.
     */
    protected $_configuration;
    
    public function __construct ($conf = null)
    {
        $this->_body = array();
        $this->_styles = array();
        $this->_sectionPr = WordDocElement::GetNewElement('SectionProperty', $this);
        $this->_docPr = WordDocElement::GetNewElement('Simple', $this, 'w:docPr');
        $this->_configuration = (!empty($conf) ? $conf : array());
    }
	
    /**
     * Return the Word document object.
     *
     * @return WordDocDocument
     */
	public function getDocument ()
	{
		return $this;
	}
    
    public function getConfig ($key, $default = null)
    {
        $value = $default;
        
        if (isset($this->_configuration[$key]))
        {
            $value = $this->_configuration[$key];
        }
        
        return $value;
    }
    
    /**
     * Set the page margins for the entire document.
     *
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @param float $left
     * @param float $header
     * @param float $footer
     * @param float $gutter
     * @return self
     */
    public function setMargins ($top, $right, $bottom, $left, $header, $footer, $gutter)
    {
        $this->_sectionPr->setMargins ($top, $right, $bottom, $left, $header, $footer, $gutter);
        return $this;
    }
    
    /**
     * Set the page size for the entire document.
     *
     * @param float $width
     * @param float $height
     * @param string $orientation
     * @param integer $code
     * @return self
     */
    public function setPageSize ($width, $height, $orientation = 'portrait', $code = 1)
    {
        $this->_sectionPr->setPageSize($width, $height, $orientation, $code);
        return $this;
    }
    
    /**
     * Specify that the first page of this document is 
     * different and will have different headers and footers.
     *
     * @return self
     */
    public function setTitlePage ()
    {
        $this->_sectionPr->setTitlePage();
        return $this;
    }
    
    /**
     * Create a header for this document. If you only have one 
     * header for this document, it must have type 'odd'.
     *
     * @param string $type - either 'first', 'even', or 'odd'
     * @return WordDocElementHeader
     */
    public function createHeader ($type)
    {
        return $this->_sectionPr->createHeader($type);
    }
    
	/**
     * Create a footer for this document. If you only have one 
     * footer for this document, it must have type 'odd'.
     *
     * @param string $type - either 'first', 'even', or 'odd'
     * @return WordDocElementFooter
     */
    public function createFooter ($type)
    {
        return $this->_sectionPr->createFooter($type);
    }
    
    /**
     * Control the view mode in Word.
     *
     * @param string $type - 'none', 'print', 'outline', 
     *                       'master-pages', 'normal', 'web'
     * @return self
     */
    public function setView ($type)
    {
        $this->_docPr->createChildElement('Simple', 'w:view')
            ->setAttribute('w:val', $type);
        return $this;
    }
    
    /**
     * Add a font to the document.
     *
     * @param string $fontName
     * @param boolean $default
     * @return self
     */
    public function addFont ($fontName, $default = false)
    {
        if (!$this->_fonts)
        {
            $this->_fonts = array();
        }
        
        if ($font = WordDocFont::LoadFont($fontName))
        {
            $this->_fonts[$fontName] = $font;
        }
        
        if ($default)
        {
            $this->_defaultFont = $fontName;
        }
        
        return $this;
    }
    
    /**
     * Add a style to the styles section of the document.
     *
     * @param WordDocElementStyle $style
     */
    public function addStyle ($style)
    {
        $this->_styles[$style->getStyleId()] = $style;
        return $this;
    }

	/**
	 * Return a style which has already be added to the styles section of the document
	 *
     * @param string $styleId
	 * @return WordDocElementStyle
	 */
	public function getStyle ($styleId)
	{
		$style = null;
			
		if (isset($this->_styles[$styleId]))
		{
			$style = $this->_styles[$styleId];
		}
		
		return $style;
	}
    
    /**
     * Create a new style element and add it to the styles section of the document.
     *
     * @param string $styleId
     * @param string $styleName - A human readable name for the style to appear in the Word style drop down.
     * @return WordDocElementStyle
     */
    public function createStyle ($styleId, $styleName)
    {
        $this->_styles[$styleId] = $style = WordDocElement::GetNewElement('Style', $this, $styleId, $styleName);
        return $style;
    }
    
    /**
     * Create a new list definition and adds it to the lists section of the document.
     *
     * @param string $type
     * @return WordDocElementListDefinition
     */
    public function createListDefinition ($type = 'SingleLevel')
    {
        $nextId = count($this->_listStyleDefs);
        $this->_listStyleDefs[] = $listDef = WordDocElement::GetNewElement('ListDefinition', $this, $type, "$nextId");
        return $listDef;
    }
    
    /**
     * Clone a list definition.
     *
     * @param WordDocElementListDefinition
     * @return WordDocElementListDefinition
     */
    public function cloneListDefinition ($listDef)
    {
		$count = count($this->_lists);
        $newListDef = clone $listDef;
        $nextId = count($this->_listStyleDefs);
        $newListDef->setId($nextId);
        $this->_listStyleDefs[] = $newListDef;
        return $newListDef;
    }
    
    /**
     * Create a new paragraph and add it to the body section of the document.
     *
     * @param mixed $style
     * @return WordDocElementParagraph
     */
    public function createParagraph ($style = null)
    {
        $paragraph = WordDocElement::GetNewElement('Paragraph', $this, $style);
        $this->_body[] = $paragraph;
        return $paragraph;
    }

    /**
     * Create a new run inside a new paragraph in this section.
     *
     * @param $style mixed
     * @return WordDocElementRun
     */
    public function createRun ($style = null)
    {
        return $this->createParagraph()->createRun($style);
    }
    
    /**
     * Create a new list and the add the reference to the lists section.
     * 
     * @param WordDocElementListDefinition $listDef
     * @param mixed $style
     * @param WordDocElement $parent
     * @return WordDocElementList
     */
    public function createList ($listDef, $style = null, $parent = null)
    {
		if ($this->_lists && in_array($listDef, $this->_lists))
		{
			$listDef = $this->cloneListDefinition($listDef);
		}
		
        $this->_lists[] = $listDef;
        $id = count($this->_lists);
		        
        $list = WordDocElement::GetNewElement('List', ($parent ? $parent : $this), "$id", $style);
        
        if ($parent)
        {
            $parent->addchild($list);
        }
        else
        {
            $this->_body[] = $list;
        }
        
        return $list;
    }
    
    /**
     * Create a new table and add it to the body section of the document.
     *
     * @param mixed $style
     * @return WordDocElementTable
     */
    public function createTable ($style = null)
    {
        $table = WordDocElement::GetNewElement('Table', $this, $style);
        $this->_body[] = $table;
        return $table;
    }
    
    /**
     * Create a new section and add it to the body section of the document.
     *
     * @return WordDocElementSection
     */
    public function createSection ()
    {
        $section = WordDocElement::GetNewElement('Section', $this);
        $this->_body[] = $section;
        return $section;
    }
    
    /**
     * Add HTML to the body of the document.  This HTML is parsed into actual
     * WordML 2003 elements.
     *
     * @param string $html
     */
    public function addHtml ($html)
    {
        $parser = new WordDocHtmlParser();
        $parser->parse($html, $this);
        return $this;
    }
    
    /**
     * Assemble the xml for the entire document and return it as a string.
     *
     * @return string
     */
    public function saveXML ()
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $wordDocument = array(
            'w:wordDocument' => array(
                '#attrs' => array(
                    'xmlns:w' => 'http://schemas.microsoft.com/office/word/2003/wordml',
                    'xmlns:wx' => 'http://schemas.microsoft.com/office/word/2003/auxHint',
                    'xmlns:aml' => 'http://schemas.microsoft.com/aml/2001/core',
                    'xmlns:v' => 'urn:schemas-microsoft-com:vml',
                    'xmlns:o' => 'urn:schemas-microsoft-com:office:office',
                   'xml:space' => 'preserve',
                )
            )
        );
        $root = WordDocDomUtils::AppendArrayToXml($dom, $wordDocument);
        
        if ($this->_fonts)
        {
            $fonts = $dom->createElement('w:fonts');
            $root->appendChild($fonts);
            
            if ($this->_defaultFont)
            {
                $defaultFont = array(
                    'w:defaultFonts' => array(
                        '#attrs' => array(
                            'w:ascii' => $this->_defaultFont,
                            'w:fareast' => $this->_defaultFont,
                            'w:h-ansi' => $this->_defaultFont,
                            'w:cs' => $this->_defaultFont,
                        )
                    )
                );
                WordDocDomUtils::AppendArrayToXml($fonts, $defaultFont);
            }
            
            foreach ($this->_fonts as $font)
            {
                $font->contributeToWordDoc($fonts, 'append');
            }
        }
        
        if ($this->_styles)
        {
            $styles = $dom->createElement('w:styles');
            $root->appendChild($styles);
            
            foreach ($this->_styles as $style)
            {
                $style->contributeToWordDoc($styles, 'append');
            }
        }
        
        if ($this->_listStyleDefs)
        {
            $lists = $dom->createElement('w:lists');
            $root->appendChild($lists);
            
            foreach ($this->_listStyleDefs as $style)
            {
                $style->contributeToWordDoc($lists, 'append');
            }
            
            if ($this->_lists)
            {
                foreach ($this->_lists as $index => $listDef)
                {
                    $list = array(
                        'w:list' => array(
                            '#attrs' => array('w:ilfo' => $index + 1),
                            'w:ilst' => array(
                                '#attrs' => array(
                                    'w:val' => $listDef->getId()
                                )
                            )
                        )
                    );
                    
                    WordDocDomUtils::AppendArrayToXml($lists, $list);
                }
            }
        }
        
        if ($this->_docPr->hasChildren())
        {
            $this->_docPr->contributeToWordDoc($root);
        }
        
        if ($this->_body)
        {
            $body = $dom->createElement('w:body');
            $root->appendChild($body);
            
            foreach ($this->_body as $element)
            {
                $element->contributeToWordDoc($body, 'append');
            }
            
            $this->_sectionPr->contributeToWordDoc($body);
        }
        
        return $dom->saveXML();
    }
    
    /**
     * Send the document to a client's browser with the given file name
     *
     * @param string $fileName
     */
    public function sendXML ($fileName)
    {
        $fileName = urlencode($fileName);
        header("Pragma: ");
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Thu, 15 Jan 1970 04:20:00 GMT"); // random time in the past
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header('Content-type: application/msword');
        header("Content-Disposition: attachment; filename=$fileName");
        print $this->saveXML();
        exit;
    }
    
    /**
     * Return itself.
     * @return self
     */
    public function end()
    {
        return $this;
    }
}

    
?>