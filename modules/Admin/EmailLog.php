<?php

class Syllabus_Admin_EmailLog extends Bss_ActiveRecord_Base
{
    
    public static function SchemaInfo ()
    {
        return array(
            '__type' => 'syllabus_email_log',
            '__pk' => array('id'),
            
            'id' => 'int',          
            'type' => 'string',         
            'creationDate' => array('datetime', 'nativeName' => 'creation_date'),
            'recipients' => 'string',
            'subject' => 'string',
            'body' => 'string',
            'attachments' => 'string',
            'success' => 'bool',

            'email' => ['1:1', 'to' => 'Syllabus_Admin_Email', 'keyMap' => ['email_id' => 'id']],
        );
    }

    public function getRecipients ()
    {
        return explode(',', $this->_fetch('recipients'));
    }

    public function setAttachments ($attachments)
    {
        $atts = array();
        foreach ($attachments as $att)
        {
            $atts[] = $att->id;
        }
        $this->_assign('attachments', (string)implode(',', $atts));
    }
    public function getAttachments ()
    {
        $attIds = explode(',', $this->_fetch('attachments'));
        $files = $this->getSchema('Syllabus_Files_File');
        
        return $files->find($files->inList($attIds));
    }
}