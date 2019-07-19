<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Materials_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_materials', $this->getDataSource('Syllabus_Materials_Materials'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('additional_information', 'string');
                $def->save();

                $def = $this->createEntityType('syllabus_materials_materials', $this->getDataSource('Syllabus_Materials_Material'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('materials_id', 'int');
                $def->addProperty('title', 'string');
                $def->addProperty('url', 'string');
                $def->addProperty('required', 'bool');
                $def->addProperty('sort_order', 'int');
                $def->save();

                break;

            case 1:

                $def = $this->alterEntityType(
                    'syllabus_materials_materials', $this->getDataSource('Syllabus_Materials_Material')
                );
                $def->addProperty('publisher', 'string');
                $def->addProperty('isbn', 'string');
                $def->save();
                
                break;

            case 2:

                $def = $this->alterEntityType(
                    'syllabus_materials_materials', $this->getDataSource('Syllabus_Materials_Material')
                );
                $def->addProperty('authors', 'string');
                $def->save();
                
                break;
        }
    }
}