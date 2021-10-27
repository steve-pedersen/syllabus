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
            $activeSemesterCodes = $semesters->findValues('internal', $semesters->active->isTrue()->andIf($semesters->endDate->afterOrEquals(new DateTime('-1 year'))));

            foreach ($activeSemesterCodes as $semesterCode)
            {
                $service->import($semesterCode);
            }

            // import schedule info
            foreach ($semesterCodes as $semesterCode)
            {
                $rs = pg_query("
                    SELECT user_id FROM syllabus_classdata_enrollments
                    WHERE year_semester = '{$semesterCode}' AND role = 'instructor' 
                ");                

                $instructors = [];
                while (($row = pg_fetch_row($rs)))
                {
                    $instructors[$row[0]] = $row[0];
                }
                
                $service->importSchedules($semesterCode, $instructors);
            }


            return true;
        }
    }

    private function schema ($recordClass)
    {
        return $this->getApplication()->schemaManager->getSchema($recordClass);
    }
}
