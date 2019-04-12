<?php

/**
 * A simple WordML 2003 element that has a name and can have 
 * child elements and attributes.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocElementSimple extends WordDocElement
{   
	public function __construct ($parent, $name)
    {
        parent::__construct($parent, $name);
    }
}


?>