<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DomUtils.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'IContributor.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Font.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'HtmlParser.php';

/**
 * This library allow the user to generate word documents which 
 * adhere to the XML Document 2003 schema.  Any document created
 * by this library should be readable by Word 2003 or greater.
 */

/**
 * Undocumented class.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocGenerator 
{
    /**
     * I don't know what a twip is, but that is how many it takes
     * to make an inch in word.
     */
    const TWIPS_PER_INCH = 1440;
    
    
	/**
	 * The dom document which contains the word content.
	 *
	 * @var DOMDocument
	 */ 
	protected $_document;
    
    /**
     * The dom node which is the current focus for editing.
     *
     * @var DOMNode
     */ 
    protected $_cursor;
    
    /**
     * The body of the document
     *
     * @var DOMNode
     */ 
    protected $_body;
    
    /**
     * The styles section of the document
     *
     * @var DOMNode
     */ 
    protected $_styles;
    
    /**
     * The root of the DOM
     *
     * @var DOMNode
     */ 
    protected $_root;
    
    /**
     * The fonts section of the document.
     *
     * @var DOMNode
     */ 
    protected $_fonts;
    
    /**
     * The next annoation id
     *
     * @var integer
     */ 
    protected $_nextAnnotationId = 1;
    
    protected $_normalFontSize = 20;
    
    public function __construct ()
    {
        $this->_document = new DOMDocument('1.0', 'utf-8');
        $this->_cursor = $this->_root = $this->_document->createElementNS(
            'http://schemas.microsoft.com/office/word/2003/wordml',
            'w:wordDocument');
        $this->addNameSpace('wx', 'http://schemas.microsoft.com/office/word/2003/auxHint');
        // $xmlnsWX = $this->_document->createAttribute('xmlns:wx');
        // $xmlnsWX->value = 'http://schemas.microsoft.com/office/word/2003/auxHint';
        // $this->_cursor->appendChild($xmlnsWX);
        $this->_document->appendChild($this->_cursor);
        $xmlSpaceAttr = $this->_document->createAttribute('xml:space');
        $xmlSpaceAttr->value = 'preserve';
        $this->_cursor->appendChild($xmlSpaceAttr);
        $this->_body = $this->_document->createElement('w:body');
        $this->_cursor->appendChild($this->_body);
        $this->_cursor = $this->_body;
    }
    
    public function startAnnotation ($name)
    {
        if ($this->_nextAnnotationId === 1)
        {
            $this->addNameSpace('aml', 'http://schemas.microsoft.com/aml/2001/core');
        }
        
        $id = $this->_nextAnnotationId++;
        
        $annotation = array(
            'aml:annotation' => array(
                '#attrs' => array(
                    'aml:id' => "$id",
                    'w:type' => 'Word.Bookmark.Start',
                    'w:name' => $name
                )
            )
        );
        
        WordDocDomUtils::AppendArrayToXml($this->_cursor, $annotation);
        return $id;
    }
    
    public function endAnnotation ($id)
    {
        $annotation = array(
            'aml:annotation' => array(
                '#attrs' => array(
                    'aml:id' => "$id",
                    'w:type' => 'Word.Bookmark.End',
                )
            )
        );
        
        WordDocDomUtils::AppendArrayToXml($this->_cursor, $annotation);
    }
    
    public function addNameSpace ($prefix, $uri)
    {
        $xmlns = $this->_document->createAttribute('xmlns:' . $prefix);
        $xmlns->value = $uri;
        $this->_root->appendChild($xmlns);
    }
    
    public function addFont ($fontName)
    {
        if (!$this->_fonts)
        {
            $this->_fonts = $this->_document->createElement('w:fonts');
            $this->_root->insertBefore($this->_fonts, $this->_body);
        }
        
        if ($font = WordDocFont::LoadFont($fontName))
        {
            $font->contributeToWordDoc($this->_fonts);
        }
    }
    
    public function addStyles ($type, $id, $styles)
    {
        if (!$this->_styles)
        {
            $this->_styles = $this->_document->createElement('w:styles');
            $this->_root->insertBefore($this->_styles, $this->_body);
        }
        
        $wStyle = array(
            '#attrs' => array(
                'w:type' => $type,
                'w:styleId' => $id
            )
        );
        
        foreach ($styles as $style)
        {
            
        }
        
        WordDocDomUtils::AppendArrayToXml($this->_styles, array('w:style' => $wStyle));
    }
    
    public function beginParagraph ($align = 'left')
    {
        $p = $this->_document->createElement('w:p');
        $this->_cursor->appendChild($p);
        $this->_cursor = $p;
        
        $wPprops = array(
            'w:pPr' => array(
                'w:jc' => array(
                    '#attrs' => array(
                        'w:val' => $align
                    )
                ),
                'w:tabs' => array(
                    'w:tab' => array(
                        array('#attrs' => array('w:val' => 'center', 'w:pos' => '1440')),
                        array('#attrs' => array('w:val' => 'left', 'w:pos' => '4320')),
                        array('#attrs' => array('w:val' => 'decimal', 'w:pos' => '7200')),
                    )
                )
            )
        );
        
        WordDocDomUtils::AppendArrayToXml($this->_cursor, $wPprops);
    }
    
    public function endParagraph ()
    {   
        if ($this->_cursor->nodeName != 'w:p')
        {
            $this->_cursor = WordDocDomUtils::FindParentByName($this->_cursor, 'w:p');
        }
        
        $this->_cursor = $this->_cursor->parentNode;
    }
    
    public function startRun ($properties = null)
    {
        if ($this->_cursor->nodeName != 'w:p')
        {
            $this->_cursor = WordDocDomUtils::FindParentByName($this->_cursor, 'w:p');
        }
        
        $wRun = $this->_document->createElement('w:r');
        $this->_cursor->appendChild($wRun);
        $this->_cursor = $wRun;
    }
    
    public function endRun ()
    {
        if ($this->_cursor->nodeName != 'w:p')
        {
            $this->_cursor = WordDocDomUtils::FindParentByName($this->_cursor, 'w:p');
        }
    }
    
    public function createHyperLink ($href, $title = '', $type = 'bookmark')
    {   
        $this->endRun();
        $wHLink = $this->_document->createElement('w:hlink');
        $this->_cursor->appendChild($wHLink);
        
        switch ($type)
        {
            case 'bookmark':
                WordDocDomUtils::AddNodeAttribute($wHLink, 'w:bookmark', $href);
                break;
            case 'href':
                WordDocDomUtils::AddNodeAttribute($wHLink, 'w:dest', $href);
                break;
        }
        
        if ($title)
        {
            WordDocDomUtils::AddNodeAttribute($wHLink, 'w:screenTip', $title);
        }
        
        $wRun = $this->_document->createElement('w:r');
        $wHLink->appendChild($wRun);
        $this->_cursor = $wRun;
    }
    
    public function startSimpleTable ($parameters, $columns)
    {
        $properties = array(
            'w:tblW' => array(
                '#attrs' => array(
                    'w:w' => '0',
                    'w:type' => 'auto'
                )
            ),
            'w:tblLook' => array(
                '#attrs' => array(
                    'w:val' => '000001E0'
                )
            ),
            'w:tblBorders' => array(
                'w:top' => array(
                    '#attrs' => array(
                        'w:val' => 'single',
                        'w:color' => 'auto',
                        'wx:bdrwidth' => '10',
                        'w:sz' => '4'
                    )
                ),
                'w:bottom' => array(
                    '#attrs' => array(
                        'w:val' => 'single',
                        'w:color' => 'auto',
                        'wx:bdrwidth' => '10',
                        'w:sz' => '4'
                    )
                ),
                'w:left' => array(
                    '#attrs' => array(
                        'w:val' => 'single',
                        'w:color' => 'auto',
                        'wx:bdrwidth' => '10',
                        'w:sz' => '4'
                    )
                ),
                'w:right' => array(
                    '#attrs' => array(
                        'w:val' => 'single',
                        'w:color' => 'auto',
                        'wx:bdrwidth' => '10',
                        'w:sz' => '4'
                    )
                ),
            )
        );
        
        if (isset($parameters['width']))
        {
            $width = $parameters['width'];
            
            if ($width != 'auto')
            {
                $width = $this->inchesToTwips($width);
                
                $properties['w:tblW'] = array(
                    '#attrs' => array(
                        'w:w' => "$width",
                    )
                );
            }
        }
        
        $properties = array('w:tblPr' => $properties);
        
        $grid['w:tblGrid'] = array('w:gridCol' => array());
        
        foreach ($columns as $column)
        {
            $width = $this->inchesToTwips($column);
            $grid['w:tblGrid']['w:gridCol'][] = array('#attrs' => array('w:w' => $width));
        }
        
        $this->startTable($properties, $grid);
    }
    
    public function startTable ($properties, $columns)
    {
        $wTbl = $this->_document->createElement('w:tbl');
        $this->_cursor->appendChild($wTbl);
        $this->_cursor = $wTbl;
        
        $wTblPr['w:tblPr'] = $properties;
        WordDocDomUtils::AppendArrayToXml($this->_cursor, $wTblPr);
        
        $wTblGrid['w:tblGrid'] = $columns;
        WordDocDomUtils::AppendArrayToXml($this->_cursor, $wTblGrid);
    }
    
    public function startRow ($properties = null)
    {
        $wTr = $this->_document->createElement('w:tr');
        $this->_cursor->appendChild($wTr);
        $this->_cursor = $wTr;
        
        if ($properties)
        {
        
        }
    }
    
    public function startCell ($properties = null)
    {
        $wTc = $this->_document->createElement('w:tc');
        $this->_cursor->appendChild($wTc);
        $this->_cursor = $wTc;
        
        if ($properties)
        {
            $wTcPr = array('w:tcPr' => array());
            
            if (isset($properties['width']))
            {
                $width = $properties['width'];
                $width = $this->inchesToTwips($width);
                
                $wTcW = array('#attrs' => array('w:w' => "$width", 'w:type' => 'dxa'));
                $wTcPr['w:tcPr']['w:tcW'] = $wTcW;
            }
            
            if (isset($properties['borders']))
            {
                $wTcPr['w:tcPr']['w:tcBorders'] = array();
                $parts = explode(',', $properties['borders']);
                
                foreach ($parts as $part)
                {
                    $wTcPr['w:tcPr']['w:tcBorders']['w:' . $part] = array(
                        '#attrs' => array(
                            'w:val' => 'single',
                            'w:color' => 'auto',
                            'wx:bdrwidth' => '10',
                            'w:sz' => '4'
                        )
                    );
                }
            }
            
            WordDocDomUtils::AppendArrayToXml($this->_cursor, $wTcPr);
        }
    }
    
    public function writeCell ($text, $width = 0, $borders = "top,bottom,left,right")
    {
        $properties = array();
        
        if ($width)
        {
            $properties['width'] = $width;
        }
        
        if ($borders)
        {
            $properties['borders'] = $borders;
        }
        
        $this->startCell($properties);
        $this->beginParagraph();
        $this->renderHtml($text);
        $this->endParagraph();
        $this->endCell();
    }
    
    public function endCell ()
    {
        $this->_cursor = WordDocDomUtils::FindParentByName($this->_cursor, 'w:tr');
    }
    
    public function endRow ()
    {
        $this->_cursor = WordDocDomUtils::FindParentByName($this->_cursor, 'w:tbl');
    }
    
    public function endTable ()
    {
        $this->_cursor = $this->_cursor->parentNode;
    }
    
    public function insertBreak ($type = 'text-wrapping')
    {
        $break = array(
            'w:br' => array(
                '#attrs' => array('w:type' => $type)
            )
        );
        WordDocDomUtils::AppendArrayToXml($this->_cursor, $break);
    }
    
    public function insertTab ($number = 1)
    {
        for ($i = 0; $i < $number; $i++)
        {
            $tab = $this->_document->createElement('w:tab');
            $this->_cursor->appendChild($tab);
        }
    }
    
    public function renderHtml($html)
    {
        $parser = new WordDocHtmlParser();
        $parser->parse($html, $this);
    }
    
    public function writeText ($text)
    {
        if ($this->_cursor->nodeName != 'w:r')
        {
            $wRun = $this->_document->createElement('w:r');
            $this->_cursor->appendChild($wRun);
            $this->_cursor = $wRun;
        }

        WordDocDomUtils::AppendArrayToXml($this->_cursor, array('w:t' => $text));
    }
    
    public function changeStyles ($styles = null)
    {
        if ($this->_cursor->nodeName != 'w:r')
        {
            $wRun = $this->_document->createElement('w:r');
            $this->_cursor->appendChild($wRun);
            $this->_cursor = $wRun;
        }
        
        $properties['w:rPr'] = array();
        
        foreach ($styles as $key => $value)
        {
            $properties['w:rPr']['w:' . $key] = array(
                '#attrs' => array(
                    'w:val' => $value
                )
            );
        }
        
        WordDocDomUtils::AppendArrayToXml($this->_cursor, $properties);
    }
    
    public function inchesToTwips ($inches)
    {
        return self::TWIPS_PER_INCH * $inches;
    }
    
    public function twipsToInches ($twips)
    {
        return $twips / floatval(self::TWIPS_PER_INCH);
    }
    
    public function echoXml ()
    {
        header("Content-type: application/xml");
        echo $this->_document->saveXML();
    }
    
    public function saveXml ()
    {
        
    }
}

?>