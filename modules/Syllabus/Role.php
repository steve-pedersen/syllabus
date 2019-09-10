<?php

/**
 * Syllabus Roles
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_Role extends Bss_ActiveRecord_BaseWithAuthorization 
{
    private $expiration;

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

    public function getExpiration ()
    {
        $now = new DateTime;
        $this->expiration = $this->expiryDate ? $now->diff($this->expiryDate) : null;
        if ($this->expiration)
        {
            $intervalString = '';
            if ($this->expiration->y)
            {
                $intervalString .= $this->expiration->format('%y-year');
                $intervalString .= $this->expiration->y > 1 ? 's ' : ' ';
            }
            if ($this->expiration->m)
            {
                $intervalString .= $this->expiration->format('%m-month');
                $intervalString .= $this->expiration->m > 1 ? 's ' : ' ';
            }
            if ($this->expiration->d)
            {
                $intervalString .= $this->expiration->format('%d-day');
                $intervalString .= $this->expiration->d > 1 ? 's ' : ' ';
            }
            if ($this->expiration->h && $intervalString === '')
            {
                $intervalString .= $this->expiration->format('%h-hour');
                $intervalString .= $this->expiration->h > 1 ? 's' : '';
            }
            $this->expiration = $intervalString;
        }

        return $this->expiration;
    }
}
