<?php

/**
 */
class Syllabus_Organizations_CronJob extends Bss_Cron_Job
{
    const PROCESS_ACTIVE_JOBS_EVERY = 60 * 24; // once a day
    
    public function run ($startTime, $lastRun, $timeDelta)
    {
        if ($timeDelta >= self::PROCESS_ACTIVE_JOBS_EVERY)
        {
            set_time_limit(0);
            ini_set('memory_limit', '-1');

            $campaigns = $this->schema('Syllabus_Syllabus_SubmissionCampaign');
            $emails = $this->schema('Syllabus_Admin_Email');

            $ongoingCampaigns = $campaigns->find(
                $campaigns->dueDate->afterOrEquals(new DateTime)
            );

            foreach ($ongoingCampaigns as $campaign)
            {
                $organization = $campaign->getOrganization();
                list($type, $id) = explode('/', $campaign->organizationAuthorizationId);

                if (lcfirst($type) === 'departments')
                {
                    if (($departmentEmail = $emails->findOne($emails->departmentId == $id)))
                    {
                        if ($this->isDue($departmentEmail))
                        {
                            $recipients = [];
                            foreach ($campaign->submissions as $submission)
                            {
                                if ($submission->status === 'open' || $submission->status === 'denied')
                                {
                                    $instructors = [];
                                    foreach ($submission->courseSection->enrollments as $enrollment)
                                    {
                                        if ($submission->courseSection->enrollments->getProperty($enrollment, 'role') === 'instructor')
                                        {
                                            $instructors[] = $enrollment;
                                        }
                                    }
                                    $instructors[] = $viewer;
                                    foreach ($instructors as $instructor)
                                    {
                                        $this->sendReminderNotification($departmentEmail, $campaign, $instructor);
                                        $recipients[] = $instructor->id;
                                    }
                                }
                            }
                            $departmentEmail->reminderSent = true;
                            $departmentEmail->recipients = implode(',', $recipients);
                            $departmentEmail->save();
                        }
                    }
                }
            }

            return true;
        }
    }

    protected function isDue ($email)
    {
        if ($email->reminderSent)
        {
            return false;
        }

        $reminderTime = DateInterval::createFromDateString($email->reminderTime);
        $today = new DateTime;
        $dateFromInterval = $today->add($reminderTime);
        $interval = $dateFromInterval->diff($email->dueDate);
        if ($interval->format('%a') == '0')
        {
            return true;
        }

        return false;
    }

    protected function sendReminderNotification ($email, $campaign, $account)
    {
        $emailManager = new Syllabus_Admin_EmailManager($this->getApplication());
        $emailData = [];
        $emailData['campaign'] = $campaign;
        $emailData['email'] = $email;
        $emailData['user'] = $account;
        $emailManager->processEmail('sendDueDateReminder', $emailData);
    }

    private function schema ($recordClass)
    {
        return $this->getApplication()->schemaManager->getSchema($recordClass);
    }
}
