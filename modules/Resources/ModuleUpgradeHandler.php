<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Resources_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_resources', $this->getDataSource('Syllabus_Resources_Resources'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('additional_information', 'string');
                $def->save();

                $def = $this->createEntityType('syllabus_resources_resources', $this->getDataSource('Syllabus_Resources_Resource'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('resources_id', 'int');
                $def->addProperty('campus_resources_id', 'int');
                $def->addProperty('image_id', 'int');
                $def->addProperty('title', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('abbreviation', 'string');
                $def->addProperty('url', 'string');
                $def->addProperty('is_custom', 'bool');
                $def->addProperty('sort_order', 'int');
                $def->save();

                break;
        }
    }
}