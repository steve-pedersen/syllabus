<?php

/**
 */
class Syllabus_Instructors_Profile extends Bss_ActiveRecord_BaseWithAuthorization
{
    const MAX_PHOTO_SIZE = 10000000; // 10MB
    
    public static $PhotoSizeProfileMap = [
        'orig' => [0, 0],
        'portrait-large' => [240, 320],
        'portrait-normal' => [120, 160],
        'portrait-small' => [30, 40],
    ];
    
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_instructors_profiles',
            '__pk' => ['id'],
            '__azidPrefix' => 'at:syllabys:instructors/Profile/',
            
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
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],

            'account' => ['1:1', 'to' => 'Bss_AuthN_Account', 'keyMap' => ['account_id' => 'id']],
            'image' => ['1:1', 'to' => 'Syllabus_Files_File', 'keyMap' => ['image_id' => 'id']],
        ];
    }
    
    public function getAuthorizationId () { return "at:syllabus:instructors/Profile/{$this->id}"; }
    
    public function getImageSrc ($reload=false)
    {
        if (!$this->_imageSrc || $reload)
        {
            if (!$this->image)
            {
                $this->_imageSrc = 'assets/images/profile-placeholder.png';
            }
            else
            {
                $this->_imageSrc = $this->image->imageSrc;
            }
        }
        return $this->_imageSrc;
    }

    public function hasUploadedImage ()
    {
        return $this->image;
    }

    public function findProfileData ($account)
    {
        $syllabi = $this->getSchema('Syllabus_Syllabus_Syllabus');
        $profiles = $this->getSchema('Syllabus_Instructors_Profile');
        $userSyllabi = $syllabi->find($syllabi->createdById->equals($account->id));
        $mostFieldsFilled = 0;
        $profileFieldsFilled = 0;
        $data = [];
        $fields = ['name','title','office','officeHours','email','phone','website','zoomAddress','credentials','about'];

        if ($userProfile = $profiles->findOne($profiles->account_id->equals($account->id)))
        {
            foreach ($fields as $field)
            {
                if (isset($userProfile->$field) && $userProfile->$field && $userProfile->$field !== '')
                {
                    $profileFieldsFilled++;
                }
            }
        }

        foreach ($userSyllabi as $syllabus)
        {
            if ($syllabus->latestVersion)
            {
                foreach ($syllabus->latestVersion->sectionVersions as $sv)
                {
                    if (isset($sv->instructor_id))
                    {
                        $instructor = null;
                        $instructorsSection = $sv->resolveSection();
                        foreach ($instructorsSection->instructors as $sectionInstructor)
                        {
                            if ($sectionInstructor->email === $account->emailAddress)
                            {
                                $instructor = $sectionInstructor;
                                break;
                            }
                        }
                        if ($instructor)
                        {
                            $fieldsFilled = 0;
                            foreach ($fields as $field)
                            {
                                $fieldsFilled = (isset($instructor->$field) && $instructor->$field !== '') ? 
                                    $fieldsFilled + 1 : $fieldsFilled;
                            }
                            
                            if ($fieldsFilled > $mostFieldsFilled)
                            {
                                $mostFieldsFilled = $fieldsFilled;
                                $data = ['syllabus' => $syllabus];
                                $data = ['instructor' => $instructor];
                            }
                            break;
                        }
                    }
                }
            }
        }

        $data['mostFields'] = 'syllabus';
        if ($mostFieldsFilled <= $profileFieldsFilled)
        {
            $data['instructor'] = $userProfile;
            $data['mostFields'] = 'profile';
        }

        return $data;
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
