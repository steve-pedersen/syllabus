<?php
	
/**
 * Font for Tahoma.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocFontTahoma extends WordDocFont
{
	public function __construct()
    {
        $this->_name = 'Tahoma';
        $this->_panose1 = '020B0604030504040204';
        $this->_charset = '00';
        $this->_fontfamily = 'Swiss';
        $this->_pitch = 'variable';
        $this->_sig = array(
            'w:usb-0' => "E1002AFF",
            'w:usb-1' => "C000605B",
            'w:usb-2' => "00000029",
            'w:usb-3' => "00000000",
            'w:csb-0' => "000101FF",
            'w:csb-1' => "00000000"
        );
    }
}

    
?>