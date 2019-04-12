<?php
	
/**
 * Font for Arial.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocFontArial extends WordDocFont
{
	public function __construct()
    {
        $this->_name = 'Arial';
        $this->_panose1 = '020B0604020202020204';
        $this->_charset = '00';
        $this->_fontfamily = 'Swiss';
        $this->_pitch = 'variable';
        $this->_sig = array(
            'w:usb-0' => "E0002AFF",
            'w:usb-1' => "C0007843",
            'w:usb-2' => "00000009",
            'w:usb-3' => "00000000",
            'w:csb-0' => "000001FF",
            'w:csb-1' => "00000000"
        );
    }
}

    
?>