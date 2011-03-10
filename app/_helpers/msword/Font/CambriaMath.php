<?php
	
/**
 * Font for CambriaMath
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocFontCambriaMath extends WordDocFont
{
	public function __construct()
    {
        $this->_name = 'Cambria Math';
        $this->_panose1 = '02040503050406030204';
        $this->_charset = '01';
        $this->_fontfamily = 'Roman';
        $this->_pitch = 'variable';
        $this->_sig = array(
            'w:usb-0' => "00000000",
            'w:usb-1' => "00000000",
            'w:usb-2' => "00000000",
            'w:usb-3' => "00000000",
            'w:csb-0' => "00000000",
            'w:csb-1' => "00000000"
        );
    }
}

    
?>