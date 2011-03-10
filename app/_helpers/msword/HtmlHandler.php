<?php
	
/**
 * A class used to handle html elements and convert them to word doc elements.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocHtmlHandler 
{
    /**
     * The annotation id for an annotation surrounding the element.
     *
     * @var integer
     */ 
    protected $_annotationId;
    
    /**
     * Get the approriate handler for an html node.
     *
     * @param string $nodeName
     * @return WordDocHtmlHandler
     */
	public static function GetHandler ($nodeName, $document)
    {
        $handler = null;
        $nodeName = ucfirst($nodeName);
        $extraPath = $document->getConfig('html-element-path-extra');

        if (($extraPath = $document->getConfig('html-element-path-extra')) !== null)
        {
            $elementPath = $extraPath . DIRECTORY_SEPARATOR . $nodeName . '.php';
            
            if (!$elementClassPrefix = $document->getConfig('html-element-class-prefix'))
            {
                $elementClass = 'WordDocHtmlHandler' . $nodeName;
            }
            else
            {
                $elementClass = $elementClassPrefix . $nodeName;
            }
            
            if (file_exists($elementPath))
            {
                require_once($elementPath);
                $handler = new $elementClass();
                return $handler;
            }
        }
        
        if (!$elementPath = $document->getConfig('html-element-path'))
        {
            $elementPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'HtmlHandler' . DIRECTORY_SEPARATOR . $nodeName . '.php';
            $elementClass = 'WordDocHtmlHandler' . $nodeName;
        }
        else
        {
            $elementPath .= DIRECTORY_SEPARATOR . $nodeName . '.php';
            if (!$elementClassPrefix = $document->getConfig('html-element-class-prefix'))
            {
                $elementClass = 'WordDocHtmlHandler' . $nodeName;
            }
            else
            {
                $elementClass = $elementClassPrefix . $nodeName;
            }
        }
        
        if (file_exists($elementPath))
        {
            require_once($elementPath);
            $handler = new $elementClass();
        }
        else
        {
            $handler = new self();
        }
        
        return $handler;
    }
    
    /**
     * Handle the initial encounter with the node.
     *
     * @param DOMNode $node
     * @param WordDocGenerator $wordDoc
     */
    public function startElementHandler ($node, $wordDocElement) 
    {
        $name = '';
        
        if ($node->hasAttribute('name') && $node->nodeName == 'A')
        {
            $name = $node->getAttribute('name');
        }
        
        if ($node->hasAttribute('id'))
        {
            $name = $node->getAttribute('id');
        }
        
        if ($name)
        {
            $wordDocElement = $wordDocElement->createAnnotation($name);
        }
        
        return $wordDocElement;
    }
    
    
    /**
     * Handle the cleanup after the encounter with the node.
     *
     * @param DOMNode $node
     * @param WordDocGenerator $wordDoc
     */
    public function endElementHandler ($node, &$wordDocElement) 
    {
        return $wordDocElement->end();
    }
}

    
    
?>