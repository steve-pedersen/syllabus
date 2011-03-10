<?php


/**
 * Utility functions
 */
class Utility {

    /**
     * Builds an obfuscated email link which will be decoded and further formatted by Javascript
     * @param string $email The email address to obfuscate
     * @return string The obfuscated email address
     */
	public function buildEmailLink($email) {
		$return = str_replace('.','&#32;&#91;dot&#93;&#32;',$email);
		$return = str_replace('@','&#32;&#91;at&#93;&#32;',$return);
		$return = '<span class="emailLink">'.$return.'</span>';
		echo $return;
	}


    /**
     * Wrapper function for redirects.  The function will properly format the URL to ensure a
     * valid redirect URL and will perform the redirect.
     * @param string $url The URL to redirect to
     */
    public function redirect($url) {
        $redirectUrl = (!preg_match('!^(https?://)?' . HOST . WEB_ROOT . '!', $url)) 
            ? BASEHREF . $url 
            : $url;
        header('Location: ' . $redirectUrl); exit;
    }
	
	
	/**
	 * Create a hash
	 * @param int $length Length of the hash to create
	 * @param bool $urlsafe Set to true to make the hash URL safe, false otherwise (defaults to true)
	 * @return string Returns the hash string
	 */
	public function createHash($length = 20, $urlsafe = true) {
		$chars_str = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,1,2,3,4,5,6,7,8,9,0,_,-';
		if(!$urlsafe) $chars_str .= '!,@,#,$,%,^&,*,(,),?,>,<,/';
		$chars_array = explode(',', $chars_str);
		$hash = '';
		while(strlen($hash) < $length) {
			$rand = array_rand($chars_array);
			$hash .= $chars_array[$rand];
		}
		return $hash;
	}
	
	
	/**
	 * Format phone numbers
	 * @param string $number The phone number
	 * @param bool $format The format to apply (defaults to the BASE format for inserting record into database and from which others can be created)
	 * @return string Return the properly formatted phone number
	 */
	public function formatPhoneNumber($number, $format = 'base') {
		// always convert to the base format first
		$number = preg_replace('![^\d]!', '', $number);
		$base = '';
		
		switch(strlen($number)) {
			case 5:
				if($number[0] == '5') {
					$base = '41540' . $number;
				} elseif($number[0] == '8') {
					$base = '41533' . $number;
				}
				break;
			
			case 7:
				$base = '415' . $number;
				break;
			
			case 10:
				$base = $number;
				break;
			
			default: $base = '';
		}
		
		// now output the desired format
		switch($format) {
			case 'print': $return = preg_replace('! (\d{3}) (\d{3}) (\d{4}) !x', '($1) $2-$3', $base); break;
			default: $return = $base;
		}
		
		return $return;
	}


}