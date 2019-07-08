<?php

/**
 * The actual resources
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Resources_Resource extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_resources_resources',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'title' => 'string',
            'description' => 'string',
            'abbreviation' => 'string',
            'url' => 'string',
            'isCustom' => ['bool', 'nativeName' => 'is_custom'],
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],
            'imageId' => ['int', 'nativeName' => 'image_id'],
            'campusResourcesId' => ['int', 'nativeName' => 'campus_resources_id'],

            'parent' => ['1:1', 'to' => 'Syllabus_Resources_Resources', 'keyMap' => ['resources_id' => 'id']],
            'image' => ['1:1', 'to' => 'Syllabus_Files_File', 'keyMap' => ['image_id' => 'id']],
            'campusResource' => ['1:1', 'to' => 'Syllabus_Syllabus_CampusResource', 'keyMap' => ['campus_resources_id' => 'id']],
        ];
    }

    public function getCampusResource ()
    {
        return $this->_fetch('campusResource');
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
