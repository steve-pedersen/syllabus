<?php

/**
 */
class Syllabus_ClassData_CronJob extends Bss_Cron_Job
{
    const PROCESS_ACTIVE_JOBS_EVERY = 60; // once a day
    
    public function run ($startTime, $lastRun, $timeDelta)
    {
        if ($timeDelta >= self::PROCESS_ACTIVE_JOBS_EVERY)
        {
            set_time_limit(0);
            
            $service = new Syllabus_ClassData_Service($this->getApplication());
            $service->importOrganizations();

            $semesters = $this->schema('Syllabus_Admin_Semester');
            $activeSemesterCodes = $semesters->findValues('internal', $semesters->active->isTrue());

            foreach ($activeSemesterCodes as $semesterCode)
            {
                $service->import($semesterCode);
            }

            return true;
        }
    }

    private function schema ($recordClass)
    {
        return $this->getApplication()->schemaManager->getSchema($recordClass);
    }
}
