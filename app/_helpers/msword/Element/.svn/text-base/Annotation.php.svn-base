<?php

/**
 * An annotation element in WordML.
 * 
 * @schema http://schemas.microsoft.com/aml/2001/core
 * @element annotation
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementAnnotation extends WordDocElement
{
    /**
     * The id of the annotation
     *
     * @var integer
     */ 
    protected $_annotationId;
    
    /**
     * The name of the annotation.
     *
     * @var string
     */ 
    protected $_annotationName;

    /**
     * Return a new id for a new annotation.
     *
     * @param integer
     */
    public static function GetNewId ()
    {
        static $Id = 1;
        
        return $Id++;
    }
    
    public function __construct ($parent, $name)
    {
        parent::__construct($parent);
        $this->_annotationId = self::GetNewId();
        $this->_annotationName = $name;
    }
    
    /**
     * Write text to the WordDoc
     *
     * @param string $text
     */
	public function addText($text)
    {
        if ($this->getParent() instanceof WordDocElementRun)
            return $this->createChildElement('Text', $text)->end();
        else
            return $this->createRun()->addText($text)->end();
    }
    
    /**
     * Create a run as a child element.
     *
     * @param mixed $style
     */
    public function createRun($style = null)
    {
        return $this->createChildElement('Run', $style);
    }
    
    /**
     * Create a paragraph as a child element.
     *
     * @param mixed $style
     */
    public function createParagraph ($style = null)
    {
        return $this->createChildElement('Paragraph', $style);
    }
    
    public function contributeToWordDoc($parent, $insertType = 'append')
    {
    	$startAnnotation = array(
            'aml:annotation' => array(
                '#attrs' => array(
                    'aml:id' => "{$this->_annotationId}",
                    'w:type' => 'Word.Bookmark.Start',
                    'w:name' => $this->_annotationName
                )
            )
        );
		WordDocDomUtils::AppendArrayToXml($parent, $startAnnotation);
		$this->childrenContributeToWordDoc($parent);

		$endAnnotation = array(
            'aml:annotation' => array(
                '#attrs' => array(
                    'aml:id' => "{$this->_annotationId}",
                    'w:type' => 'Word.Bookmark.End',
                )
            )
        );
		WordDocDomUtils::AppendArrayToXml($parent, $endAnnotation);
    }
}


?>