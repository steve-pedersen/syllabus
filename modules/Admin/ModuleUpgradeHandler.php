<?php

/**
 * Upgrade/Install this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Admin_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {
        $app = $this->getApplication();
        $settings = $app->siteSettings;
        
        switch ($fromVersion)
        {
            case 0:
                $def = $this->createEntityType('syllabus_semesters', $this->getDataSource('Syllabus_Admin_Semester'));
                $def->addProperty('id', 'int', array('sequence' => true, 'primaryKey' => true));
                $def->addProperty('display', 'string');               
                $def->addProperty('internal', 'string');
                $def->addProperty('start_date', 'datetime');
                $def->addProperty('end_date', 'datetime');
                $def->addProperty('active', 'bool');
                $def->save();

                break;

            case 1:
                $settings->defineProperty('email-testing-only', 'If set, email will only be sent to the test address. SHOULD ONLY TURNED ON FOR TESTING.', 'int');
                $settings->defineProperty('email-test-address', 'Email address for testing. If set, all emails will be sent here.', 'string');

                $def = $this->createEntityType('syllabus_email_log', $this->getDataSource('Syllabus_Admin_EmailLog'));
                $def->addProperty('id', 'int', array('sequence' => true, 'primaryKey' => true));
                $def->addProperty('type', 'string');               
                $def->addProperty('recipients', 'string');
                $def->addProperty('creation_date', 'datetime');
                $def->addProperty('subject', 'string');
                $def->addProperty('body', 'string');
                $def->addProperty('attachments', 'string');
                $def->addProperty('success', 'bool');
                $def->save();    

                $def = $this->createEntityType('syllabus_emails', $this->getDataSource('Syllabus_Admin_Email'));
                $def->addProperty('id', 'int', array('sequence' => true, 'primaryKey' => true));
                $def->addProperty('type', 'string');               
                $def->addProperty('recipients', 'string');
                $def->addProperty('creation_date', 'datetime');
                $def->addProperty('subject', 'string');
                $def->addProperty('body', 'string');
                $def->addProperty('signature', 'string');
                $def->addProperty('contact_email', 'string');
                $def->addProperty('attachments', 'string');
                $def->addProperty('success', 'bool');
                $def->addProperty('reminder_time', 'string');
                $def->addProperty('department_id', 'int');
                $def->save();    

                break;

            case 2:
                $def = $this->alterEntityType('syllabus_emails', $this->getDataSource('Syllabus_Admin_Email'));
                $def->addProperty('reminder_sent', 'bool');
                $def->save();
                break;
        }
    }
}