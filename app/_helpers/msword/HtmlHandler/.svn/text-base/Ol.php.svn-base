<?php
	
/**
 * Handle 'ol' tags in html.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocHtmlHandlerOl extends WordDocHtmlHandler
{
    public static function GetListDefinition ($wordDocElement)
    {
        static $def = null;
        
        if ($def === null)
        {
            $def = $wordDocElement->getDocument()
            ->createListDefinition('HybridMultilevel')
                ->createLevel()
                    ->setStartNumber(1)
                    ->setBulletText("%1.")
                    ->getParagraphProperty()
                        ->setIndent(array('left' => 0.5, 'hanging' => 0.25))
                    ->end()
                ->end()
                ->createLevel()
                    ->setStartNumber(1)
                    ->setBulletText("%2.")
                    ->setNumberType(WordDocElementListLevel::NFC_LC_LETTER)
                    ->getParagraphProperty()
                        ->setIndent(array('left' => 1, 'hanging' => 0.25))
                    ->end()
                ->end();
        }
        
        return $def;
    }
	/**
     * Handle the initial encounter with the node.
     *
     * @param DOMNode $node
     * @param WordDocGenerator $wordDoc
     */
    public function startElementHandler ($node, $wordDocElement) 
    {
        $wordDocElement = parent::startElementHandler($node, $wordDocElement);
        
        if ($wordDocElement instanceof WordDocElementParagraph) 
        {
            $parent = $wordDocElement->getParent();
            
            if ($parent instanceof WordDocElementList)
            {
                return $parent->createList();
            }
            else
            {
                return $parent->createList(self::GetListDefinition($wordDocElement));
            }
        }
        else
        {
            return $wordDocElement->createList(self::GetListDefinition($wordDocElement));
        }
    }
}

    
?>