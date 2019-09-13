<?php

/**
 */
class Syllabus_Instructors_Controller extends Syllabus_Master_Controller
{
    public static $imageTypes = ['image/gif', 'image/jpeg', 'image/tiff', 'image/png'];

    public static function getRouteMap ()
    {
        return [       
            '/profile/:id' => ['callback' => 'profile', ':id' => '[0-9]+'],
            '/profile/:id/upload' => ['callback' => 'upload', ':id' => '[0-9]+'],
        ];
    }

    public function profile ()
    {
        $this->setPageTitle('Edit Profile');
        $viewer = $this->requireLogin();
        $profileAccount = $this->requireExists($this->helper('activeRecord')->fromRoute('Bss_AuthN_Account', 'id'));
        if (!$this->hasPermission('admin') && $viewer->id !== $profileAccount->id)
        {
            $this->accessDenied('nope');
        }

        $profiles = $this->schema('Syllabus_Instructors_Profile');
        $profile = $profiles->findOne($profiles->account_id->equals($profileAccount->id));
        $data = $profiles->createInstance()->findProfileData($profileAccount) ?? $profiles->createInstance();

        if ($this->request->wasPostedByUser())
        {
            switch ($this->getPostCommand())
            {
                case 'save':
                    $profile = $profile ?? $profiles->createInstance();
                    $profile->absorbData($this->request->getPostParameters());
                    $profile->account_id = $profileAccount->id;
                    $profile->modifiedDate = new DateTime;
                    $profile->save();

                    $this->flash('Your profile has been updated', 'success');
                    $this->response->redirect('syllabi');
                    break;
            }
        }
        
        $this->template->fillFromSyllabus = (
            (!$profile && $data) ||
            ($profile && $data && isset($data['mostFields']) && $data['mostFields'] === 'syllabus')
        );
        $this->template->profileData = $data;
        $this->template->profile = $profile;
        $this->template->account = $profileAccount;
        $this->template->profileImage = $profile ? $profile->imageSrc : $profiles->createInstance()->imageSrc;
    }

    public function upload ()
    {
        $results = [
            'message' => 'Server error when uploading.',
            'status' => 500,
            'success' => false
        ];

        if ($this->request->wasPostedByUser())
        {
            $files = $this->schema('Syllabus_Files_File');
            $file = $files->createInstance();
            $file->createFromRequest($this->request, 'file', false, self::$imageTypes);
            // $file->createFromRequest($this->request, 'file', false);
            
            if ($file->isValid())
            {
                $uploadedBy = (int)$this->request->getPostParameter('uploadedBy');
                $file->uploaded_by_id = $uploadedBy;
                $file->moveToPermanentStorage();
                $file->save();

                $profiles = $this->schema('Syllabus_Instructors_Profile');
                $profile = $profiles->findOne($profiles->account_id->equals($uploadedBy));
                $profile->image_id = $file->id;
                $profile->modifiedDate = new DateTime;
                $profile->save();

                $results = [
                    'message' => 'Your file has been uploaded.',
                    'status' => 200,
                    'success' => true,
                    'imageSrc' => 'files/' . $file->id . '/download'
                ];
            }
            else
            {
                $results['message'] = $file->getValidationMessages();
            }
        }

        if ($this->request->getPostParameter('ajax'))
        {
            echo json_encode($results);
            exit;            
        }
        else
        {
            if ($profile)
            {
                $this->response->redirect('profile/' . $profile->account_id);
            }
            $this->response->redirect('syllabi');
        }


    }

}
            