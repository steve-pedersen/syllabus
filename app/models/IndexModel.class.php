<?php


/**
 * Index Model
 */
class IndexModel extends BaseModel {	

    
    /**
     * Submit the contact form
     */
    public function doContactForm() {
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
        $Mailer->setBody($this->body);
        
        if(count($Mailer->getErrors())) {
            $return = false;
            foreach($Mailer->getErrors() as $k => $v) {
                $this->error_messages[] = $v;
            }
        } else {
            if($Mailer->sendMail()) {
                $return = true;
                $this->success_messages[] = 'Your message has been sent successfully.';
            } else {
                $return = false;
                $this->error_messages[] = 'An unknown error occurred while attmepting to send your email.';
            }
        }
            
        return $return;
    }


}
