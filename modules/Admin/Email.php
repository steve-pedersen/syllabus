<?php

class Syllabus_Admin_Email extends Bss_ActiveRecord_Base
{
    
    public static function SchemaInfo ()
    {
        return array(
            '__type' => 'syllabus_emails',
            '__pk' => array('id'),
            
            'id' => 'int',          
            'type' => 'string',         
            'creationDate' => array('datetime', 'nativeName' => 'creation_date'),
            'recipients' => 'string',
            'subject' => 'string',
            'body' => 'string',
            'signature' => 'string',
            'contactEmail' => ['string', 'nativeName' => 'contact_email'],
            'attachments' => 'string',
            'success' => 'bool',
            'reminderTime' => ['string', 'nativeName' => 'reminder_time'],
            'departmentId' => ['int', 'nativeName' => 'department_id'],

            'department' => ['1:1', 'to' => 'Syllabus_AcademicOrganizations_Department', 'keyMap' => ['department_id' => 'id']],
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