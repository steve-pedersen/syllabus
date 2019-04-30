<?php

/**
 */
class Syllabus_ClassData_CronJob extends Bss_Cron_Job
{
    const PROCESS_ACTIVE_JOBS_EVERY = 60 * 24; // once a day
    
    public function run ($startTime, $lastRun, $timeDelta)
    {
        if ($timeDelta >= self::PROCESS_ACTIVE_JOBS_EVERY)
        {
            set_time_limit(0);
            
            $service = new Syllabus_ClassData_Service($this->application);
            $service->importOrganizations();

            $semesterCodes = $this->application->siteSettings->semester;
            if (!is_array($semesterCodes))
            {
                $semesterCodes = explode(',', $semesterCodes);
            }
            foreach ($semesterCodes as $semesterCode)
            {
                $service->import($semesterCode);
            }
            return true;
        }
    }
}
