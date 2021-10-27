<?php

/**
 */
class Syllabus_ClassData_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {
        $siteSettings = $this->getApplication()->siteSettings;
        switch ($fromVersion)
        {
            case 0:
                $siteSettings->defineProperty('classdata-api-url', 'The URL for the ClassData/SIMS API', 'string');
                $siteSettings->defineProperty('classdata-api-key', 'The key for the ClassData/SIMS API', 'string');
                $siteSettings->defineProperty('classdata-api-secret', 'The secret for the ClassData/SIMS API', 'string');
            
                $def = $this->createEntityType('syllabus_classdata_courses', $this->getDataSource('Syllabus_ClassData_Course'));
                $def->addProperty('id', 'string', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->addProperty('deleted', 'bool');
                $def->addProperty('department_id', 'int');
                $def->save();

                $def = $this->createEntityType('syllabus_classdata_course_sections', $this->getDataSource('Syllabus_ClassData_CourseSection'));
                $def->addProperty('id', 'string', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('section_number', 'string');
                $def->addProperty('class_number', 'string');
                $def->addProperty('semester', 'string');
                $def->addProperty('year', 'string');
                $def->addProperty('title', 'string');   
                $def->addProperty('description', 'string');     
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->addProperty('deleted', 'bool');
                $def->addProperty('course_id', 'string');
                $def->addProperty('syllabus_id', 'int');
                $def->addProperty('department_id', 'int');
                $def->addIndex('course_id');
                $def->save();

                $def = $this->createEntityType('syllabus_classdata_users', $this->getDataSource('Syllabus_ClassData_User'));
                $def->addProperty('id', 'string', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('first_name', 'string');
                $def->addProperty('last_name', 'string');
                $def->addProperty('email_address', 'string');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->addProperty('deleted', 'bool');
                $def->save();

                $def = $this->createEntityType('syllabus_classdata_enrollments', $this->getDataSource('Syllabus_ClassData_Enrollment'));
                $def->addProperty('course_section_id', 'string', ['primaryKey' => true]);
                $def->addProperty('user_id', 'string', ['primaryKey' => true]);
                $def->addProperty('role', 'string');
                $def->addProperty('year_semester', 'string');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->addProperty('deleted', 'bool');
                $def->save();

                $def = $this->createEntityType('classdata_sync_logs', $this->getDataSource('Syllabus_ClassData_SyncLog'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('dt', 'datetime');
                $def->addProperty('by', 'string');
                $def->addProperty('status', 'int');
                $def->addProperty('error_code', 'string');
                $def->addProperty('error_message', 'string');
                $def->save();

                $app = $this->getApplication();
                $settings = $app->siteSettings;
                $settings->defineProperty('semesters', 'The comma-separated list of semesters to pay attention to, each in format (YYYS where S=3 is Spring, S=7 is Fall). The first 3 digits shall be digits 1, 3, and 4 of the year, e.g. 2019=219.', 'string');

                
                $app = $this->getApplication();
                $settings = $app->siteSettings;
                $settings->defineProperty('classdata-default-email', 'A default email address that will be applied to users when they do have one provided by the CS datasource.', 'string');
                break;
            
            case 1:
                $def = $this->createEntityType('syllabus_classdata_course_schedules', $this->getDataSource('Syllabus_ClassData_CourseSchedule'));
                $def->addProperty('id', 'string', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('term_year', 'string');
                $def->addProperty('course_type', 'string');
                $def->addProperty('course_section_id', 'string');
                $def->addProperty('faculty_id', 'string');
                $def->addProperty('account_id', 'int');
                $def->addProperty('user_deleted', 'bool');
                $def->addProperty('schedules', 'string');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->addIndex('course_section_id');
                $def->addIndex('faculty_id');
                $def->save();  
                break;
        }
    }
}