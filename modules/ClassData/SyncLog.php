<?php

/**
 */
class Syllabus_ClassData_SyncLog extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'classdata_sync_logs',
            '__pk' => ['id'],
            
            'id' => 'int',
            'dt' => 'datetime',
            'by' => 'string',
            'status' => 'int',
            'errorCode' => ['string', 'nativeName' => 'error_code'],
            'errorMessage' => ['string', 'nativeName' => 'error_message'],
        ];
    }
}
