<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Organizations_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_groups', $this->getDataSource('Syllabus_Organizations_Group'));
                $def->addProperty('id', 'int', array('primaryKey' => true, 'sequence' => true));
                $def->addProperty('name', 'string');
                $def->addProperty('abbreviation', 'string');
                $def->addProperty('display_name', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->save();

                break;

            case 1:

                $def = $this->alterEntityType('syllabus_groups', $this->getDataSource('Syllabus_Organizations_Group'));
                $def->addProperty('is_system_level', 'string');
                $def->save();

                break;
        }
    }
}