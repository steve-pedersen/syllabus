#!/usr/bin/php -q

<?php

/**
 * This cron script is run by the crontab on the server and is used to update the system's databases from the SIMS snapshots.
 * We have to hardcode the include for the config file to make sure it is found since the cron file is outside the web root
 * Once the config file is included, we can make use of the normal application constants for other includes, etc
 */

require '/var/www/syllabus/app/config.php';

// only run the cron if CRON has not been disabled in the config file
if(DISABLE_CRON_SCRIPT == false) {
    require (APP_ROOT . 'models/BaseModel.class.php');
    require (APP_ROOT . 'models/SystemModel.class.php');
    $System = new SystemModel();
    $System->systemUpdate();
}