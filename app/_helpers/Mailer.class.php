<?php

/**
 * Mail class.  Handles all send mail requests
 */
class Mailer {
    
    /**
     * @var array Array to hold errors
     */
    private $_errors = array();
    
    /**
     * @var string The string of email addresses
     */
    private $_to = '';
    
    /**
     * @var string Cc addresses
     */
    private $_cc = '';
    
    /**
     * @var string Bcc addresses
     */
    private $_bcc = '';
    
    /**
     * @var string The From address
     */
    private $_from = '';
    
    /**
     * @var string The Reply-To address
     */
    private $_reply_to = '';
    
    /**
     * @var string The subject of the message
     */
    private $_subject = '';
    
    /**
     * @var string The body of the message
     */
    private $_body = '';
    
    
    
    
    /**
     * Constructor
     */
    public function __construct() {
        
    }
    
    
    /**
     * Retrive the array of errors
     */
    public function getErrors() {
        return $this->_errors;
    }
    
    
    /**
     * Set the To address(es)
     * @var string Email addresses
     * @return bool Returns true if the emails are valid, false otherwise
     */
    public function setTo($to) {
        if($this->validateAddresses($to)) {
            $this->_to = $to;
            $return = true;
        } else {
            $return = false;
        }
        
        return $return;
    }
    
    
    /**
     * Set the From address
     * @var string $from Email address to be used for the From
     * @return bool Returns true if valid, false otherwise
     */
    public function setFrom($from) {
        if(!empty($from)) {
            if($this->validateAddresses($from)) {
                $this->_from = $from;
                $return = true;
            } else {
                $return = false;
            }            
        } else {
            $this->_errors[] = 'You must enter your email address';
            $return = false;
        }
        
        return $return;
    }
    
    
    /**
     * Set the Cc address(es)
     * @var string $cc Email address to be used for the Cc
     * @return bool Returns true if valid, false otherwise
     */
    public function setCc($cc) {
        if($this->validateAddresses($cc)) {
            $this->_cc = $cc;
            $return = true;
        } else {
            $return = false;
        }
        
        return $return;
    }
    
    
    /**
     * Set the Bcc address(es)
     * @var string $bcc Email address to be used for the Bcc
     * @return bool Returns true if valid, false otherwise
     */
    public function setBcc($bcc) {
        if($this->validateAddresses($bcc)) {
            $this->_bcc = $bcc;
            $return = true;
        } else {
            $return = false;
        }
        
        return $return;
    }
    
    
    /**
     * Set the Reply-To address
     * @var string $reply_to Email address to be used for Reply-To
     * @return bool Returns true if email is valid, false otherwise
     */
    public function setReplyTo($reply_to) {
        if($this->validateAddresses($reply_to)) {
            $this->_reply_to = $reply_to;
            $return = true;
        } else {
            $return = false;
        }
        
        return $return;
    }
    
    
    /**
     * Set the subject line
     * @var string $subject Email subject
     * @return bool Returns true if the subject is valid, false otherwise
     */
    public function setSubject($subject) {
        if(!$this->containsNewLines($subject)) {
            $this->_subject = $subject;
            $return = true;
        } else {
            $this->_errors[] = 'Invalid Subject';
            $return = false;
        }
        
        return $return;
    }
    
    
    /**
     * Set the email body
     * @var string $email Email body
     * @return bool Returns true
     */
    public function setBody($body) {
        $this->_body = wordwrap($body, 72);
        return true;
    }
    
    
    /**
     * Send the mail
     * @return bool Returns true if the mail is successfully sent, false otherwise
     */
    public function sendMail() {
        $headers_array = array();
        $headers_array[] = 'Cc: ' . $this->_cc;
        $headers_array[] = 'Bcc: ' . $this->_bcc;
        $headers_array[] = 'From: ' . $this->_from;
        $headers_array[] = isset($this->_reply_to)
            ? 'Reply-To: ' . $this->_reply_to
            : 'Reply-To: ' . $this->_from;
        $headers_array[] = 'MIME-Version: 1.0';
        $headers_array[] = 'Content-type: text/html; charset=iso-8859-1';
        $headers = implode("\n", $headers_array);
        
        return mail($this->_to, $this->_subject, $this->_body, $headers);
    }
	
	
    /**
     * Validate an email address or multiple addresses (separated by comma)
     * @param string $addresses The email addresses to validate
     * @return bool Returns true if valid, false otherwise
     */
	private function validateAddresses($addresses) {
        $return = true;
        if(!empty($addresses)) {
            $addresses_array = explode(',', $addresses);
            foreach($addresses_array as $k => $v) {
                // very simple email validation (just check for an @ sign)
                if(strpos($v, '@') === false) {
                    $return = false;
                    $this->_errors[] = '<span style="font-weight: bold;">' . $v . '</span> is not a valid email address';
                }
            }
        }
        
        return $return;
	}


    /**
     * Check whether a string contains new lines to prevent header injection
     * @param string $test_string The string to test
     * @return bool Returns true if there are no new lines, false otherwise
     */
    private function containsNewLines($test_string) {
        return (preg_match('! (\n+) | (\r+) | (\t+) | (%0A+) | (%0D+) | (%08+) | (%09+) !x', $test_string))
            ? true
            : false;
    }

}

?>