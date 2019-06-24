<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Policies_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_policies', $this->getDataSource('Syllabus_Policies_Policies'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->save();

                $def = $this->createEntityType('syllabus_policies_policies', $this->getDataSource('Syllabus_Policies_Policy'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('policies_id', 'int');
                $def->addProperty('title', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('sort_order', 'int');
                $def->save();

                break;
        }
    }
}