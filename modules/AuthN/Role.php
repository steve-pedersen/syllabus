<?php

/**
 */
class Syllabus_AuthN_Role extends Bss_ActiveRecord_BaseWithAuthorization
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_authn_roles',
            '__pk' => ['id'],
            '__azidPrefix' => 'at:syllabus:authN/Role/',
            
            'id' => 'int',
            'name' => 'string',
            'description' => 'string',
            'isSystemRole' => ['bool', 'nativeName' => 'is_system_role'],
            
            'accounts' => ['N:M', 'to' => 'Bss_AuthN_Account', 'via' => 'syllabus_authn_account_roles', 'toPrefix' => 'account', 'fromPrefix' => 'role'],
        ];
    }
}
