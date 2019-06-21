<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Objectives_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_objectives', $this->getDataSource('Syllabus_Objectives_Objectives'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->save();

                $def = $this->createEntityType('syllabus_objectives_objectives', $this->getDataSource('Syllabus_Objectives_Objective'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('objectives_id', 'int');
                $def->addProperty('title', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('sort_order', 'int');
                $def->save();

                break;
        }
    }
}