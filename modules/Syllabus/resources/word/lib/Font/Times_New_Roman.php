<?php
	
/**
 * Font for Times New Roman.
 * 
 * @author	Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright	Copyright &copy; San Francisco State University.
 */
class WordDocFontTimes_New_Roman extends WordDocFont
{
	public function __construct()
    {
        $this->_name = 'Times New Roman';
        $this->_panose1 = '02020603050405020304';
        $this->_charset = '00';
        $this->_fontfamily = 'Roman';
        $this->_pitch = 'variable';
        $this->_sig = array(
            'w:usb-0' => "E0002AEF",
            'w:usb-1' => "C0007841",
            'w:usb-2' => "00000009",
            'w:usb-3' => "00000000",
            'w:csb-0' => "000001FF",
            'w:csb-1' => "00000000"
        );
    }
}
    

?>