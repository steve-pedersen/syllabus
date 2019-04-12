<?php
	
/**
 * Any class which directly contributes content or markup to a WordML 2003
 * document must implement this interface.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
interface WordDocIContributor
{
    /**
     * Contribute XML to the Word document by inserting XML into the parent
     *
     * @param DOMNode $parent
     * @param string $insertType - The values can be: 'append' or 'prepend'
     */
	public function contributeToWordDoc ($parent, $insertType = 'append');
}

?>