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

            foreach ($ongoingCampaigns as $ongoingCampaign)
            {
                $organization = $ongoingCampaign->getOrganization();
                list($type, $id) = explode('/', $ongoingCampaign->organizationAuthorizationId);

                if (lcfirst($type) === 'departments')
                {
                    if (($departmentEmail = $emails->findOne($emails->departmentId == $id)))
                    {
                        if ($this->isDue($departmentEmail, $ongoingCampaign))
                        {
                            $instructors = [];

                            foreach ($ongoingCampaign->submissions as $submission)
                            {
                                if ($submission->status === 'open' || $submission->status === 'denied')
                                {
                                    foreach ($submission->courseSection->enrollments as $enrollment)
                                    {
                                        if ($submission->courseSection->enrollments->getProperty($enrollment, 'role') === 'instructor')
                                        {
                                            $instructors[$enrollment->id] = $enrollment;
                                        }
                                    }
                                }
                            }

                            $counter = 0;
                            $length = 40;
                            do
                            {
                                $tempUsers = array_slice($instructors, $counter, $length);
                                $this->sendReminderNotification($departmentEmail, $ongoingCampaign, $tempUsers);
                                $counter += $length;
                            }
                            while ($counter <= (count($instructors) - 1));

                            $numRecipients = count($instructors);
                            $ongoingCampaign = $this->requireExists($ongoingCampaigns->get($ongoingCampaignId));
                            $ongoingCampaign->log .= "
                            <li>
                                A reminder email was sent to {$numRecipients} instructors for the {$ongoingCampaign->semester->display} semester.
                            </li>";
                            $ongoingCampaign->save();
                        }
                    }
                }
            }

            return true;
        }
    }

    protected function isDue ($email, $campaign)
    {
        if ($email->reminderSent)
        {
            return false;
        }

        $reminderTime = DateInterval::createFromDateString($email->reminderTime);
        $today = new DateTime;
        $dateFromInterval = $today->add($reminderTime);
        $interval = $dateFromInterval->diff($campaign->dueDate);
        if ($interval && $interval->format('%a') == '0')
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
