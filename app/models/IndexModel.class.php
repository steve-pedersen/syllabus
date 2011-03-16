<?php


/**
 * Index Model
 */
class IndexModel extends BaseModel {	

    
    /**
     * Submit the contact form
     */
    public function contactForm() {
        $Mailer = new Mailer();
        $to = (isset($this->to) && !empty($this->to))
            ? $this->to
            : 'workshop@sfsu.edu';
        $Mailer->setTo($to);
        $Mailer->setFrom($this->from);
        $subject = (isset($this->subject) && !empty($this->subject))
            ? $this->subject
            : 'Message via Syllabus website';
        $Mailer->setSubject($subject);
		
        // remove \r\n which get added via BaseModel::__set() and the mysqli->real_escape_string()
        // this is pretty hackish ... need to change the sanitize so that it only runs before DB entry or have a way
        // to prevent fields from being escaped
        $body = preg_replace('!\\\r\\\n!', '', $this->body);
        $Mailer->setBody($body);
        
        if(count($Mailer->getErrors()) > 0) {
            $return = false;
            foreach($Mailer->getErrors() as $k => $v) {
                Messages::addMessage($v, 'error');
            }
        } else {
            if($Mailer->sendMail()) {
                $return = true;
                Messages::addMessage('Your message has been sent successfully.', 'success');
            } else {
                $return = false;
                Messages::addMessage('An unknown error occurred while attmepting to send your email.', 'error');
            }
        }
        
        return $return;
    }


}
