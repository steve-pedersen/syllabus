<?php

/**
 * A list property for a paragraph in a WordML 2003 document.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementListProperty extends WordDocElement
{   
    public function __construct ($parent, $listDefId, $level)
    {
        parent::__construct($parent, 'w:listPr');
        $this->createChildElement('Simple', 'w:ilvl')->setAttribute('w:val', $level);
        $this->createChildElement('Simple', 'w:ilfo')->setAttribute('w:val', $listDefId);
    }
}


?>