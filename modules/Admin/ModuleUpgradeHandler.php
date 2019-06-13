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

        }
    }
}