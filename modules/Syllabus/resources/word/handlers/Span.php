<?php
	
/**
 * Handle 'span' tags in html.  If the span has the class
 * "pub-journal-title" or "pub-book-title", it should italicize
 * the resulting run.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class Catalog_Html_Handler_Span extends WordDocHtmlHandler
{
	/**
     * Handle the initial encounter with the node.
     *
     * @param DOMNode $node
     * @param WordDocGenerator $wordDoc
     */
    public function startElementHandler ($node, $wordDocElement) 
    {
        $wordDocElement = parent::startElementHandler($node, $wordDocElement);
        if ($node->hasAttribute('class'))
        {
            $className = $node->getAttribute('class');
            
            if (strpos($className, 'pub-book-title') !== false ||
                strpos($className, 'pub-journal-title') !== false
            )
                return $wordDocElement->createRun('Emphasis');
        }

        return $wordDocElement->createRun();
    }
}

    
?>