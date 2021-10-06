<?php

/**
 * The actual title/description instructor items
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Instructors_Instructor extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_instructors_instructors',
            '__pk' => ['id'],
            
            'id' => 'int',
            'name' => 'string',
            'title' => 'string',
            'office' => 'string',
            'officeHours' => ['string', 'nativeName' => 'office_hours'],
            'email' => 'string',
            'phone' => 'string',
            'website' => 'string',
            'zoomAddress' => ['string', 'nativeName' => 'zoom_address'],
            'credentials' => 'string',
            'about' => 'string',
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],
            // 'imageId' => ['int', 'nativeName' => 'image_id'],

            'image' => ['1:1', 'to' => 'Syllabus_Files_File', 'keyMap' => ['image_id' => 'id']],
            'instructorsSection' => ['1:1', 'to' => 'Syllabus_Instructors_Instructors', 'keyMap' => ['instructors_id' => 'id']],
        ];
    }

    // TODO: get a default profile pic
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
