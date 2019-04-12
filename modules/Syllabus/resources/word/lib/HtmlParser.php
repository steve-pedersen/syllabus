<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'HtmlHandler.php';
	
/**
 * A class to parse HTML and convert it to word doc elements
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocHtmlParser
{
	/**
 	 * The htmlDOM
 	 *
 	 * @var DOMDocument
 	 */ 
    protected $_htmlDom;
    
    public function __construct ()
    {
        $this->_htmlDom = new DOMDocument('1.0', 'utf-8');
    }
    
    /**
     * Parse the HTML and add it to the word doc element.
     *
     * @param string $html
     * @param WordDocElement $wordDocElement
     */
    public function parse ($html, $wordDocElement)
    {
        $html = '<html><head><meta http-equiv="Content-type" content="text/html; charset=utf-8"></head><body>' . $html . '</body></html>';
        @$this->_htmlDom->loadHTML($html);
        $body = $this->_htmlDom->getElementsByTagName('body');
        $this->traverse($body->item(0), $wordDocElement);
    }
    
    /**
     * Peform an inorder traversal of the html and have the appropriate html
     * handler handle each html node
     *
     * @param DomNode $node
     * @param WordDocElement $wordDocElement
     */
    public function traverse ($node, $wordDocElement = null)
    {
        if ($node)
        {
            switch ($node->nodeType)
            {
                case XML_ELEMENT_NODE:
                    $nodeName = strtolower($node->nodeName);
                    $htmlHandler = WordDocHtmlHandler::GetHandler($nodeName, $wordDocElement->getDocument());
                    $newWordDocElement = $htmlHandler->startElementHandler($node, $wordDocElement);

                    foreach ($node->childNodes as $childNode)
                    {
                        $this->traverse($childNode, $newWordDocElement);
                    }
                    
                    $htmlHandler->endElementHandler($node, $wordDocElement);
                    break;
                    
                case XML_TEXT_NODE:
                    if (!preg_match('/^\s+$/', $node->nodeValue) || $wordDocElement instanceof WordDocElementRun)
                        if (!method_exists($wordDocElement, 'addText'))
                        {
                            $text = preg_replace('/\s+/', ' ', $node->nodeValue);
                            $wordDocElement->createParagraph()->addText($text);
                        }
                        else
                        {
                            $text = preg_replace('/\s+/', ' ', $node->nodeValue);
                            $wordDocElement->addText($text);
                        }
                    break;
            }
        }
    }
}
    

?>