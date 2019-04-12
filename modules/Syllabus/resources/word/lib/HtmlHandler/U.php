<?php
	
/**
 * Handle 'u' tags in html.  The word document should implement
 * a style called 'Underline' to style 'u' tags.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocHtmlHandlerU extends WordDocHtmlHandler
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
        return $wordDocElement->createRun('Underline');
    }
}

    
?>