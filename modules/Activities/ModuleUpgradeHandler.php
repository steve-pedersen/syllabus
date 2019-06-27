<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Activities_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_activities', $this->getDataSource('Syllabus_Activities_Activities'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->save();

                $def = $this->createEntityType('syllabus_activities_activities', $this->getDataSource('Syllabus_Activities_Activity'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('activities_id', 'int');
                $def->addProperty('name', 'string');
                $def->addProperty('value', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('sort_order', 'int');
                $def->save();

                break;
        }
    }
}