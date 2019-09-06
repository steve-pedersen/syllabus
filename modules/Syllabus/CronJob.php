<?php

/**
 */
class Syllabus_Syllabus_CronJob extends Bss_Cron_Job
{
    const PROCESS_ACTIVE_JOBS_EVERY = 60 * 24; // once a day
    
    public function run ($startTime, $lastRun, $timeDelta)
    {
        if ($timeDelta >= self::PROCESS_ACTIVE_JOBS_EVERY)
        {
            set_time_limit(0);
            
            $this->removeExpiredSyllabusRoles();

            return true;
        }
    }

    public function removeExpiredSyllabusRoles ()
    {
        $syllabi = $this->schema('Syllabus_Syllabus_Syllabus');
        $syllabusRoles = $this->schema('Syllabus_Syllabus_Role');
        $authZ = $this->getApplication()->authorizationManager;

        $expiredRoles = $syllabusRoles->find(
            $syllabusRoles->expiryDate->before(new DateTime)
        );

        foreach ($expiredRoles as $expiredRole)
        {
            $azids = $authZ->getSubjectsWhoCan('syllabus edit', $expiredRole);
            array_merge($azids, $authZ->getSubjectsWhoCan('syllabus clone', $expiredRole));
            if ($users = $this->schema('Bss_AuthN_Account')->getByAzids($azids))
            {
                foreach ($users as $user)
                {
                    $authZ->revokePermission($user, 'syllabus edit', $expiredRole);
                    $authZ->revokePermission($user, 'syllabus clone', $expiredRole);
                }
            }
            $expiredRole->delete();
        }
        $authZ->updateCache();
    }

    private function schema ($recordClass)
    {
        return $this->getApplication()->schemaManager->getSchema($recordClass);
    }
}
