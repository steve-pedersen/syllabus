<?php

/**
 * External resources, guidelines, and documents to be shared on Overview page.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_SharedResource extends Bss_ActiveRecord_Base
{
    private $_fileSrc;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_shared_resources',
            '__pk' => ['id'],
            
            'id' => 'int',   
            'title' => 'string',
            'description' => 'string',
            'url' => 'string',
            'iconClass' => ['string', 'nativeName' => 'icon_class'],
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],
            'fileId' => ['int', 'nativeName' => 'file_id'],
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'active' => 'bool',

            'file' => ['1:1', 'to' => 'Syllabus_Files_File', 'keyMap' => ['file_id' => 'id']],
        ];
    }

    public function getFileSrc ($reload=false)
    {
        if (!$this->_fileSrc || $reload)
        {
            $this->_fileSrc = $this->file->getDownloadUrl();
        }
        return $this->_fileSrc;
    }
}
