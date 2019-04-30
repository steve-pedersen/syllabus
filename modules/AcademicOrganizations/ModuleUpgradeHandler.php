<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_AcademicOrganizations_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_departments', $this->getDataSource('Syllabus_AcademicOrganizations_Department'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('college_id', 'int');
                $def->addProperty('name', 'string');
                $def->addProperty('abbreviation', 'string');
                $def->addProperty('display_name', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('external_key', 'string');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->save();

                $def = $this->createEntityType('syllabus_colleges', $this->getDataSource('Syllabus_AcademicOrganizations_College'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('name', 'string');
                $def->addProperty('abbreviation', 'string');
                $def->addProperty('display_name', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->save();

                break;
        }
    }
}