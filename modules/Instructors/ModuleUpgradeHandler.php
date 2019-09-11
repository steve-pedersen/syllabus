<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Instructors_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_instructors', $this->getDataSource('Syllabus_Instructors_Instructors'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->save();

                $def = $this->createEntityType('syllabus_instructors_instructors', $this->getDataSource('Syllabus_Instructors_Instructor'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('instructors_id', 'int');
                $def->addProperty('name', 'string');
                $def->addProperty('title', 'string');
                $def->addProperty('office', 'string');
                $def->addProperty('office_hours', 'string');
                $def->addProperty('email', 'string');
                $def->addProperty('phone', 'string');
                $def->addProperty('website', 'string');
                $def->addProperty('zoom_address', 'string');
                $def->addProperty('credentials', 'string');
                $def->addProperty('about', 'string');
                $def->addProperty('sort_order', 'int');
                $def->addProperty('image_id', 'int');
                $def->save();

                break;

            case 1:

                $def = $this->createEntityType('syllabus_instructors_profiles', $this->getDataSource('Syllabus_Instructors_Profile'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('account_id', 'int');
                $def->addProperty('name', 'string');
                $def->addProperty('title', 'string');
                $def->addProperty('office', 'string');
                $def->addProperty('office_hours', 'string');
                $def->addProperty('email', 'string');
                $def->addProperty('phone', 'string');
                $def->addProperty('website', 'string');
                $def->addProperty('zoom_address', 'string');
                $def->addProperty('credentials', 'string');
                $def->addProperty('about', 'string');
                $def->addProperty('image_id', 'int');
                $def->addProperty('modified_date', 'datetime');
                $def->save();

                break;
        }
    }
}