<?php

/**
 */
class Syllabus_AuthN_AccessLevel extends Bss_ActiveRecord_BaseWithAuthorization
{
    public static function SchemaInfo ()
    {
        return array(
            '__class' => 'Syllabus_AuthN_AccessLevelSchema',
            '__type' => 'syllabus_authn_access_levels',
            '__pk' => array('id'),
            '__azidPrefix' => 'at:syllabus:authN/AccessLevel/',
            
            'id' => 'int',
            'name' => 'string',
            'description' => 'string',
        );
    }
}
