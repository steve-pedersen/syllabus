<?php
	
/**
 * Handle 'li' tags in html.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocHtmlHandlerLi extends WordDocHtmlHandler
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
        
        if (!($wordDocElement instanceof WordDocElementParagraph || $wordDocElement instanceof WordDocElementRun))
        {
            return $wordDocElement->createParagraph();
        }
        return $wordDocElement;
    }
}

    
?>