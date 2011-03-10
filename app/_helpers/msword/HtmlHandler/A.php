<?php
	
/**
 * Handle 'a' tags in html. The word document should implement
 * a style called 'HyperLink' to style 'a' tags.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocHtmlHandlerA extends WordDocHtmlHandler
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
        
        if ($node->hasAttribute('href'))
        {
            $href = $node->getAttribute('href');
            $type = 'href';
            
            if (strpos($href, '#') === 0)
            {
                $type = 'bookmark';
            }
            
            $title = '';
            
            if ($node->hasAttribute('title'))
            {
                $title = $node->getAttribute('title');
            }
            
            if(method_exists($wordDocElement, 'createHyperLink'))
            {
                return $wordDocElement->
                    createHyperLink()->setReference($href)->setTitle($title);
            }
            else
            {
                return $wordDocElement
                    ->createParagraph()
                    ->createHyperLink()
                    ->setReference($href)->setTitle($title);
            }
        }
        
        return $wordDocElement;
    }
}

    
?>