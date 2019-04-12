<?php

/**
 * A representation of a run element in WordML
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementRun extends WordDocElement
{
	const ShapeTypeID = '_x0000_t75';
	
    static protected $_shapeType;
	
    /**
     * The styles for the run.
     *
     * @var WordDocElementStyle
     */ 
    protected $_styles;

	/**
	 * Whether the run has a style reference in one of it run properties.
	 *
	 * @var string
	 **/
	protected $_hasDocumentedStyle = false;
	
	protected $_propertyElements;
	
    
    public function __construct ($parent, $style = null)
    {
        parent::__construct($parent, 'w:r');
        $this->_styles = array();
        $this->_propertyElements = array();

        if (($parent instanceof self  || $parent instanceof WordDocElementHyperLink) && ($parentStyle = $parent->getStyles()) != null)
       	{
            $this->_styles = $parentStyle;

			if ($parent->hasDocumentedStyle())
			{
				$this->_hasDocumentedStyle = true;
			}
        }

        if ($style)
        {
			if (!$this->_hasDocumentedStyle && is_string($style))
			{
				$this->_hasDocumentedStyle = true;
			}
			else
			{
				$style = $this->_document->getStyle($style);
			}
			
            $this->_styles[] = $style;
        }
        
        if ($this->_styles)
        {
            foreach ($this->_styles as $style)
            {
                $property = $this->createChildElement('RunProperty');
				$this->_propertyElements[] = $property;
                $property->setStyle($style);
            }
        }
    }

	/**
	 * Determine if the run has a run property with a style reference.
	 *
	 * @return bool
	 **/
	public function hasDocumentedStyle ()
	{
		return $this->_hasDocumentedStyle;
	}
    
    /**
     * Add a tab to the run.
     *
     * @return self
     */
    public function addTab ()
    {
        $this->createChildElement('Simple', 'w:tab');
        return $this;
    }
    
    /**
     * Add text to the run.
     *
     * @param string $text
     * @return self
     */
	public function addText ($text)
    {
        $this->createChildElement('Text', $text);
        return $this;
    }
    
    /**
     * Add instruction text to the run.
     *
     * @param string $text
     * @return self
     */
    public function addInstructionText($text)
    {
        $this->createChildElement('InstructionText', $text);
        return $this;
    }
    
    /**
     * Add html to the run.
     *
     * @param string $html
     * @return self
     */
    public function addHtml ($html)
    {
        $parser = new WordDocHtmlParser();
        $parser->parse($html, $this);
        return $this;
    }
    
    public function addImage ($file, $width, $height, $title, $position = 'relative', $top = 0, $left = 0)
    {
        $data = @file_get_contents($file);
        
        if ($data)
        {
            $data = base64_encode($data);
            $pict = $this->createChildElement('Simple', 'w:pict');
            
			$this->getShapeType($pict);
            $dataElement = $pict->createChildElement('Simple', 'w:binData');
            $dataElement->setAttribute('w:name', 'http://010101010.gif');
            $dataElement->createChildElement('CharacterData', $data);
			$imageElement = $pict->createChildElement('Simple', 'v:shape');
            $style = "width:{$width}pt;height:{$height}pt;";
            $imageElement->setAttribute('style', $style)
				->setAttribute('id', '_x0000_i1025')
				->setAttribute('type', self::ShapeTypeID)
				->createChildElement('Simple', 'v:imagedata')
					->setAttribute('src', 'http://010101010.gif')
					->setAttribute('o:title', $title);
        }
        
        return $this;
    }
    
    /**
     * Create a hyper link in the run.
     *
     * @param mixed $style
     * @return WordDocElementHyperLink
     */
    public function createHyperLink ($style = null)
	{
		$this->_children[] = $hyperLink = WordDocElement::GetNewElement('HyperLink', $this, $style);
        return $hyperLink;
	}
    
    /**
     * Create another run.
     *
     * @param mixed $style
     * @return WordDocElementRun
     */
    public function createRun ($style = null)
    {
        $run = $this->createChildElement('Run', $style);
        return $run;
    }

    /**
     * Have the run continue on a new line.
     *
     * @param string $type
     * @return self
     */
	public function addBreak ($type = 'text-wrapping')
    {
        $break = $this->createChildElement('Simple', 'w:br');
		$break->setAttribute('w:type', $type);
		return $this;
    }
    
    /**
     * Contribute XML to the Word document by inserting XML into the parent
     *
     * @param DOMNode $parent
     * @param string $insertType - The values can be: 'append' or 'prepend'
     */
	public function contributeToWordDoc ($parent, $insertType = 'append')
    {
        $run = WordDocDomUtils::AppendArrayToXML($parent, array($this->_name => ''));

        foreach ($this->_children as $element)
        {
            if (!($element instanceof $this || $element instanceof WordDocElementHyperLink))
            {
                $element->contributeToWordDoc($run, 'append');
            }
            else
            {
				$element->contributeToWordDoc($parent, 'append');
				$run = WordDocDomUtils::AppendArrayToXML($parent, array($this->_name => ''));
				
				foreach ($this->_propertyElements as $property)
				{
					$property->contributeToWordDoc($run, 'prepend');
				}
            }
        }
    }
    
    /**
     * Return the styles of this run.
     *
     * @return mixed
     */
    public function getStyles ()
    {
       return $this->_styles;
    }
    
    protected function getShapeType ($pict)
    {
        if (!self::$_shapeType)
		{
			self::$_shapeType = $pict->createChildElement('Simple', 'v:shapetype')
				->setAttribute('id', self::ShapeTypeID)
				->setAttribute('coordsize', '21600,21600')
				->setAttribute('o:spt', '75')
				->setAttribute('o:preferrelative', 't')
				->setAttribute('path', 'm@4@5l@4@11@9@11@9@5xe')
				->setAttribute('filled', 'f')
				->setAttribute('stroked', 'f');
			self::$_shapeType->createChildElement('Simple', 'v:stroke');
			self::$_shapeType->createChildElement('Simple', 'v:formulas')
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'if lineDrawn pixelLineWidth 0')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'sum @0 1 0')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'sum 0 0 @1')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'prod @2 1 2')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'prod @3 21600 pixelWidth')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'prod @3 21600 pixelHeight')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'sum @0 0 1')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'prod @6 1 2')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'prod @7 21600 pixelWidth')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'sum @8 21600 0')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'prod @7 21600 pixelHeight')->end()
				->createChildElement('Simple', 'v:f')->setAttribute('eqn', 'sum @10 21600 0')->end();
			self::$_shapeType->createChildElement('Simple', 'v:path')
				->setAttribute('o:extrusionok', 'f')
				->setAttribute('gradientshapeok', 't')
				->setAttribute('o:connecttype', 'rect');
			self::$_shapeType->createChildElement('Simple', 'o:lock')
				->setAttribute('v:ext', 'edit')
				->setAttribute('aspectratio', 't');
		}
    }
}


?>