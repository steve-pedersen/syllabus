<?php

/**
 */
class Syllabus_Instructors_Controller extends Syllabus_Master_Controller
{
    public static function getRouteMap ()
    {
        return [       
            '/profile/:id' => ['callback' => 'profile', ':id' => '[0-9]+'],
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
        $profile = $profiles->findOne($profiles->account_id->equals($viewer->id));
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
    }

}
            