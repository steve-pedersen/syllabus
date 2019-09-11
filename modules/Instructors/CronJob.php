<?php

/**
 */
class Syllabus_Instructors_CronJob extends Bss_Cron_Job
{
    const PROCESS_ACTIVE_JOBS_EVERY = 60 * 24; // once a day
    
    public function run ($startTime, $lastRun, $timeDelta)
    {
        if ($timeDelta >= self::PROCESS_ACTIVE_JOBS_EVERY)
        {
            set_time_limit(0);
            
            $this->generateDefaultProfiles();

            return true;
        }
    }

    public function generateDefaultProfiles ()
    {
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $profiles = $this->schema('Syllabus_Instructors_Profile');
        $accounts = $this->schema('Bss_AuthN_Account');
        $roles = $this->schema('Syllabus_AuthN_Role');

        $facultyRole = $roles->findOne($roles->name->equals('Faculty'));
        $profileIds = $profiles->findValues('id', $profiles->account_id->isNotNull());
        $otherAccounts = $accounts->find($accounts->id->notInList($profileIds));

        foreach ($otherAccounts as $account)
        {
            if ($account->roles->has($facultyRole))
            {
                $profile = $profiles->createInstance();
                $data = $profile->findProfileData($account);
                if (!empty($data) && isset($data['instructor']))
                {
                    $profile->absorbData($data['instructor']);
                }
                $profile->email = $profile->email ?? $account->emailAddress;
                $profile->name = $profile->name ?? $account->fullName;
                $profile->account_id = $account->id;
                $profile->modifiedDate = new DateTime;
                $profile->save();
            }
        }
    }

    private function schema ($recordClass)
    {
        return $this->getApplication()->schemaManager->getSchema($recordClass);
    }
}
