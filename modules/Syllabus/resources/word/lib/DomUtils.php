<?php
	
/**
 * Utility class to help with assembling the XML of the WordDocDocument.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocDomUtils 
{
    /**
     * Convert an array to xml and append it to an xml node.
     *
     * @param DomNode $parent
     * @param array $array
     * @return DomNode
     */
	public static function AppendArrayToXml ($parent, $array)
    {
        $domNode = null;
        
        if ($array)
        {
            $dom = ($parent->ownerDocument ? $parent->ownerDocument : $parent);
            
            foreach ($array as $key => $value)
            {
                if ($key == '#attrs' && !is_int($key)) // Add attributes to parent
                {
                    foreach ($value as $attrName => $attrValue)
                    {
                        self::AddNodeAttribute($parent, $attrName, $attrValue);
                    }
                }
                elseif ($key == '#cdata')
                {
                    $textNode = $dom->createTextNode($value);
                    $parent->appendChild($textNode);
                }
                elseif (is_array($value) && !empty($value)) // Add elements to parent
                {
                    if (self::ArrayIsAssociative($value))
                    {
                        $domNode = $dom->createElement($key);
                        self::AppendArrayToXml($domNode, $value);
                        $parent->appendChild($domNode);
                    }
                    else // Add elements with the same node name to parent
                    {
                        foreach ($value as $item)
                        {
                            $newArray = array($key => $item);
                            self::AppendArrayToXml($parent, $newArray);
                        }
                    }
                }
                elseif ($value || $value === 0 || $value === "0") // Add element with simple value to parent
                {
                    $domNode = $dom->createElement($key);
					$cdata = $dom->createCDATASection($value);
					$domNode->appendChild($cdata);
                    $parent->appendChild($domNode);
                }
                else // Add empty element to parent
                {
                    $domNode = $dom->createElement($key);
                    $parent->appendChild($domNode);
                }
            }
        }
        
        if ($domNode)
        {
            return $domNode;
        }
        else
        {
            return $parent;
        }
    }
    
    public static function insertArrayInXML($node, $array, $insert = 'after')
    {
        $dom = $node->ownerDocument;
        $next = $node;
        $parent = $node->parentNode;
        
        foreach ($array as $key => $value)
        {
            $domNode = $dom->createElement($key);
            
            if ($insert == 'after')
            {
                if ($next->nextSibling)
                {
                    $parent->insertBefore($domNode, $next->nextSibling);
                }
                else
                {
                    $parent->appendChild($domNode);
                }
                
                $next = $domNode;
            }
            else
            {
                $parent->insertBefore($domNode, $node);
            }
            
            self::AppendArrayToXml($domNode, $value);
        }
    }
    
    public static function AddNodeAttribute($node, $attrName, $attrValue = null)
    {
        $dom = $node->ownerDocument;
        $attr = $dom->createAttribute($attrName);
        
        if ($attrValue !== null)
        {
            // $textNode = $dom->createTextNode((string)$attrValue);
            // $attr->appendChild($textNode);
            $attr->value = $attrValue;
        }
        
        $node->appendChild($attr);
    }
    
    public static function FindParentByName ($node, $nodeName)
    {
        $dom = $node->ownerDocument;
        $parent = $node->parentNode;
        
        while ($parent->nodeName != $nodeName)
        {
            if ($parent->nodeName == $dom->firstChild->nodeName)
            {
                throw new Exception("$nodeName called at wrong time.");
            }
            
            $parent = $parent->parentNode;
        }
        
        return $parent;
    }
    
    public static function ArrayIsAssociative($array)
    {
        $keys = array_keys($array);
        $kkeys = array_keys($keys);
        return ($keys !== $kkeys);
    }
}

    
    
?>