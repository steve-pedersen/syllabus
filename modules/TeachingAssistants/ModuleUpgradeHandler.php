<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_TeachingAssistants_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_teaching_assistants', $this->getDataSource('Syllabus_TeachingAssistants_TeachingAssistants'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->save();

                $def = $this->createEntityType('syllabus_teaching_assistants_teaching_assistants', $this->getDataSource('Syllabus_TeachingAssistants_TeachingAssistant'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('teaching_assistants_id', 'int');
                $def->addProperty('name', 'string');
                $def->addProperty('email', 'string');
                $def->addProperty('additional_information', 'string');
                $def->addProperty('sort_order', 'int');
                $def->save();

                break;
        }
    }
}