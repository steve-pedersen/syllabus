<?php

/**
 * Campus Resource items, which go into Resource Section Type
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_CampusResource extends Bss_ActiveRecord_Base
{
    private $_imageSrc;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_campus_resources',
            '__pk' => ['id'],
            
            'id' => 'int',   
            'title' => 'string',
            'abbreviation' => 'string',
            'description' => 'string',
            'url' => 'string',
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],
            'imageId' => ['int', 'nativeName' => 'image_id'],
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'deleted' => 'bool',

            'image' => ['1:1', 'to' => 'Syllabus_Files_File', 'keyMap' => ['image_id' => 'id']],
            'tags' => ['N:M',
                'to' => 'Syllabus_Resources_Tag',
                'via' => 'syllabus_campus_resources_tags_map',
                'fromPrefix' => 'campus_resources',
                'toPrefix' => 'tags',
                'orderBy' => ['name']
            ],
        ];
    }

    public function getImageSrc ($reload=false)
    {
        if (!$this->_imageSrc || $reload)
        {
            if (!$this->image)
            {
                $this->_imageSrc = 'assets/images/SFState_V_rgb.jpg';
            }
            else
            {
                $this->_imageSrc = $this->image->imageSrc;
            }
        }
        return $this->_imageSrc;
    }
}
