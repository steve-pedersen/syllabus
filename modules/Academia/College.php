<?php

/**
 */
class Syllabus_Academia_College extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return array(
            '__type' => 'syllabus_colleges',
            '__pk' => array('id'),
            '__azidPrefix' => 'at:syllabus:academia/College/',
            
            'id' => 'int',
            'name' => 'string',
            'abbreviation' => 'string',
            'displayName' => array('string', 'nativeName' => 'display_name'),
            'description' => 'string',
            
            'contactName' => array('string', 'nativeName' => 'contact_name'),
            'contactEmail' => array('string', 'nativeName' => 'contact_email'),
            'contactPhone' => array('string', 'nativeName' => 'contact_phone'),
            
            'createdDate' => array('datetime', 'nativeName' => 'created_date'),
            'modifiedDate' => array('datetime', 'nativeName' => 'modified_date'),

            'departments' => array('1:N', 'to' => 'Syllabus_Academia_Department'),
        );
    }
    // TODO: figure out which one to use
    // public function getAuthorizationId () { return "{$this->__azidPrefix}{$this->id}"; }
    public function getAuthorizationId () { return "at:syllabus:academia/Group/{$this->id}"; }

    // TODO: figure out which one to use
    // public function getEntity ()
    public function getConcreteEntity ()
    {
        // TODO: figure out which one to use
        // return $this->getSchema(get_class($this))->get($this->id);
        return $this;
    }

}
