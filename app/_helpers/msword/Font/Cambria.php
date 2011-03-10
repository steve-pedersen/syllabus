<?php
	
/**
 * Font for Cambria.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocFontCambria extends WordDocFont
{
	public function __construct()
    {
        $this->_name = 'Cambria';
        $this->_panose1 = '02040503050406030204';
        $this->_charset = '00';
        $this->_fontfamily = 'Roman';
        $this->_pitch = 'variable';
        $this->_sig = array(
            'w:usb-0' => "A00002EF",
            'w:usb-1' => "4000004B",
            'w:usb-2' => "00000000",
            'w:usb-3' => "00000000",
            'w:csb-0' => "0000009F",
            'w:csb-1' => "00000000"
        );
    }
}

    
?>