<?php

/**
 * Syllabus Roles
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_Role extends Bss_ActiveRecord_BaseWithAuthorization 
{

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_roles',
            '__pk' => ['id'],
            '__azidPrefix' => 'at:syllabus:syllabus/Role/',
            
            'id' => 'int',   
            'name' => 'string',
            'description' => 'string',
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'expiryDate' => ['datetime', 'nativeName' => 'expiry_date'],

            'syllabus' => ['1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => ['syllabus_id' => 'id']],
        ];
    }

    public function getAuthorizationId ()
    {
        return 'at:syllabus:syllabus/Role/' . $this->id;
    }

    public function getIsExpired ()
    {
        return $this->expiryDate && $this->expiryDate < new DateTime;
    }
}
