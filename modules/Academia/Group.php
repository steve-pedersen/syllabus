<?php

/**
 */
class Syllabus_Academia_Group extends Syllabus_Academia_Entity
{   
    public static function SchemaInfo ()
    {
        return array(
            '__type' => 'syllabus_groups',
            '__pk' => array('id'),
            '__azidPrefix' => 'at:syllabus:academia/Group/',
            
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
