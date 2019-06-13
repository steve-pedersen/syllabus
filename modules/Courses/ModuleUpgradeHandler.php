<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Courses_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_courses', $this->getDataSource('Syllabus_Courses_Course'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('title', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('section_number', 'string');
                $def->addProperty('class_number', 'string');
                $def->addProperty('semester', 'string');
                $def->addProperty('year', 'string');
                $def->addProperty('external_key', 'string');
                $def->save();

                break;
        }
    }
}