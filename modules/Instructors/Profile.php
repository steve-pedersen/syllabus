<?php

/**
 */
class Syllabus_Instructors_Profile extends Bss_ActiveRecord_BaseWithAuthorization
{
    const MAX_PHOTO_SIZE = 2097152; // 2MB
    
    public static $PhotoSizeProfileMap = [
        'orig' => [0, 0],
        'portrait-large' => [240, 320],
        'portrait-normal' => [120, 160],
        'portrait-small' => [30, 40],
    ];
    
	private $progress;
	
	private $filteredLists;
	
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'fresca_faculty_profiles',
            '__pk' => ['id'],
            '__azidPrefix' => 'csu:catalog:faculty/Profile/',
            
            'id' => 'int',
            'account' => ['1:1', 'to' => 'Bss_AuthN_Account'],
            'displayName' => ['string', 'nativeName' => 'display_name'],
            'aboutMe' => ['string', 'nativeName' => 'about_me'],
            'office' => 'string',
            'phone' => 'string',
            'displayEmail' => ['string', 'nativeName' => 'display_email'],
            'positionTitle' => ['string', 'nativeName' => 'position_title'],

            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],
        ];
    }
    
    public function getAuthorizationId () { return "at:syllabus:instructors/Profile/{$this->id}"; }
    
    public function getFirstName ()
    {
        return $this->account->firstName;
    }
    
    public function getMiddleName ()
    {
        return $this->account->middleName;
    }
    
    public function getLastName ()
    {
        return $this->account->lastName;
    }
    
    public function getPhone ()
    {
        $value = $this->_fetch('phone');
        
        if (strlen($value) == 10)
        {
            return preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '\1-\2-\3', $value);
        }
        elseif (strlen($value) == 7)
        {
            return preg_replace('/([0-9]{3})([0-9]{4})/', '\1-\2', $value);
        }
        
        return $value;
    }
    
    public function setPhone ($value)
    {
        $value = preg_replace('/[^0-9]+/', '', $value);
        
        if (!empty($value))
        {
            if (strlen($value) == 11 && $value[0] == '1')
            {
                // Strip the leading one.
                $value = substr($value, 1);
            }
            
            if (strlen($value) == 7)
            {
                $this->invalidate('phone', 'Please use a full, 10-digit telephone number, including the area code.');
            }
            elseif (strlen($value) < 10)
            {
                $this->invalidate('phone', 'Please use a full, 10-digit telephone number (for example, <samp>415-555-1234</samp>).');
            }
            elseif (strlen($value) > 10)
            {
                $this->invalidate('phone', 'Please use a 10-digit US telephone number (for example, <samp>415-555-1234</samp>). We do not currently support international or other non-US telephone numbers.');
            }
        }
        
        $this->_assign('phone', $value);
    }
    
    /**
     * @return string
     */
    public function getHref ()
    {
        return "faculty/{$this->id}";
    }
    
    /**
     */
    public function getEditHref ()
    {
        return "faculty/{$this->id}/edit";
    }
    
    public function getNameLink ()
    {
        return '<a href="' . $this->getHref() . '">' . htmlspecialchars($this->displayName) . '</a>';
    }
    
    public function getWebsiteAnchorList ()
    {
        $anchorList = array();
        
        foreach ($this->websiteList as $website)
        {
            $anchorList[] = $website->getAnchor(array('newWindow' => true, 'rel' => 'me'));
        }
        
        return $anchorList;
    }

	public function hasVisible ($type)
	{
		return $this->getFilteredList($type, 'isVisible')->count();
	}
    
    public function getHasPhoto ()
    {
        return ($this->getPhotoFile('portrait-normal', true) !== null);
    }
    
    public function getPhotoFile ($sizeProfile = 'portrait-normal', $checkExists = true)
    {
        $photoFile = Bss_Core_PathUtils::path(
            $this->getApplication()->getConfiguredPath('app', 'var'),
            'faculty-photos',
            "faculty-{$this->id}-{$sizeProfile}.png"
        );
        return (!$checkExists || file_exists($photoFile) ? $photoFile : null);
    }
    
    public function createPhotoFromFile ($filename)
    {
        $valid = true;

        if (!is_readable($filename))
        {
            $this->invalidate('photo', 'Fresca experienced an internal error trying to read the uploaded image. Please try again.');
            $this->getApplication()->log('error', 'Could not read image for createPhotoFromFile: ' . $filename . ' (faculty id = ' . $this->id . ')');
            $valid = false;          
        }
        
        if (filesize($filename) > self::MAX_PHOTO_SIZE)
        {
            $this->invalidate('photo', 'Sorry, the image you uploaded is too large. Please choose an image smaller than ' . Bss_Core_Utils::formatBytes(self::MAX_PHOTO_SIZE));
            $valid = false;
        }
        
        if ($valid)
        {           
            $img = @imageCreateFromString(file_get_contents($filename));

            if ($img === false)
            {
                $this->invalidate('photo', 'Unsupported image format or corrupt image file.');
                return false;
            }
            
            $widthCurrent = imagesx($img);
            $heightCurrent = imagesy($img);
            $ratio = $widthCurrent / $heightCurrent;

            // if image doesn't have aspect ratio of 3:4, crop off even amount on sides of image
            if ($ratio !== 0.75)
            {     
                $cropped = $filename . "_cropped";
                if ($ratio < 0.75) // taller than it is wide, crop bottom
                {
                    $heightCropped = (4 * $widthCurrent) / (3 * $heightCurrent) * $heightCurrent;
                    $xCoord = 0;
                    $yCoord = 0;
                    $heightCurrent = $heightCropped;
                }   
                else 
                {
                    $widthCropped = 3 * $heightCurrent / 4;
                    $xCoord = ($widthCurrent - $widthCropped) / 2;
                    $yCoord = 0;
                    $widthCurrent = $widthCropped;                    
                }    

                // trim widthCurrent to ratio 3:4 of heightCurrent, center the crop
                $image = new Imagick($filename);
                $image->cropImage($widthCurrent, $heightCurrent, $xCoord, $yCoord);
                $image->writeImage($cropped);
                $img = @imageCreateFromString(file_get_contents($cropped));
            }
            
            // Now cut the different size profiles off of it.
            foreach (self::$PhotoSizeProfileMap as $sizeProfile =>& $dims)
            {
                list($widthDesired, $heightDesired) = $dims;
                $scaledPhotoFile = $this->getPhotoFile($sizeProfile, false);
                
                if ($widthDesired == 0)
                {
                    // No scaling to perform.
                    imagePng($img, $scaledPhotoFile);
                }
                else
                {                    
                    $imgScaled = imageCreateTrueColor($widthDesired, $heightDesired);
                    
                    // Set background color.
                    $bgColor = imageColorAllocate($imgScaled, 0, 0, 0);
                    imageFill($imgScaled, 0, 0, $bgColor);
                    
                    // Scale down while preserving aspect ratio.
                    // (If the image is smaller than desired, then we scale by a factor of 1, which disables scaling).
                    $scale = max(($widthCurrent / $widthDesired), ($heightCurrent / $heightDesired), 1);
                    $widthScaled = $widthCurrent / $scale;
                    $heightScaled = $heightCurrent / $scale;
                    
                    // Center it.
                    $xCenter = ($widthDesired - $widthScaled) / 2;
                    $yCenter = ($heightDesired - $heightScaled) / 2;
                    
                    // Resample and write to disk.
                    imageCopyResampled($imgScaled, $img, $xCenter, $yCenter, 0, 0, $widthScaled, $heightScaled, $widthCurrent, $heightCurrent);
                    imagePng($imgScaled, $scaledPhotoFile);
                    imageDestroy($imgScaled);
                }
            }
            
            imageDestroy($img);
        }
        
        return $valid;
    }
    

}
