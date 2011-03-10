<?php

/**
 * A section property element for a section in a WordML document.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementSectionProperty extends WordDocElement
{
    public function __construct ($parent)
    {
        parent::__construct($parent, 'w:sectPr');
    }
    
    /**
     * Set the page margins for the section
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
        $top = floor($top * WordDocDocument::TWIPS_PER_INCH);
        $right = floor($right * WordDocDocument::TWIPS_PER_INCH);
        $bottom = floor($bottom * WordDocDocument::TWIPS_PER_INCH);
        $left = floor($left * WordDocDocument::TWIPS_PER_INCH);
        $header = floor($header * WordDocDocument::TWIPS_PER_INCH);
        $footer = floor($footer * WordDocDocument::TWIPS_PER_INCH);
        $gutter = floor($gutter * WordDocDocument::TWIPS_PER_INCH);
        $this->createChildElement('Simple', 'w:pgMar')
            ->setAttribute('w:top', $top)
            ->setAttribute('w:right', $right)
            ->setAttribute('w:bottom', $bottom)
            ->setAttribute('w:left', $left)
            ->setAttribute('w:header', $header)
            ->setAttribute('w:footer', $footer)
            ->setAttribute('w:gutter', $gutter);
        
        return $this;
    }
    
    /**
     * Set the page size for the section.
     *
     * @param float $width
     * @param float $height
     * @param string $orientation
     * @param integer $code
     * @return self
     */
    public function setPageSize ($width, $height, $orientation = 'portrait', $code = 1)
    {
        $width = floor($width * WordDocDocument::TWIPS_PER_INCH);
        $height = floor($height * WordDocDocument::TWIPS_PER_INCH);
        $this->createChildElement('Simple', 'w:pgSz')
            ->setAttribute('w:w', $width)
            ->setAttribute('w:h', $height)
            ->setAttribute('w:orient', $orientation)
            ->setAttribute('w:code', $code);
        
        return $this;
    }
    
    /**
     * Specify that the first page of this section is 
     * different and will have different headers and footers.
     *
     * @return self
     */
    public function setTitlePage ()
    {
        $this->createChildElement('Simple', 'w:titlePg');
        return $this;
    }
    
    /**
     * Create a header for this section. If you only have one 
     * header for this section, it must have type 'odd'.
     *
     * @param string $type - either 'first', 'even', or 'odd'
     * @return WordDocElementHeader
     */
    public function createHeader ($type)
    {
        return $this->createChildElement('Header', $type);
    }
    
    /**
     * Create a footer for this section. If you only have one 
     * footer for this section, it must have type 'odd'.
     *
     * @param string $type - either 'first', 'even', or 'odd'
     * @return WordDocElementFooter
     */
    public function createFooter ($type)
    {
        return $this->createChildElement('Footer', $type);
    }
}


?>