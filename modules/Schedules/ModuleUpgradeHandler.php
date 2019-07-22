<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Schedules_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_schedules', $this->getDataSource('Syllabus_Schedules_Schedules'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('columns', 'int');
                $def->addProperty('header1', 'string');
                $def->addProperty('header2', 'string');
                $def->addProperty('header3', 'string');
                $def->addProperty('header4', 'string');
                $def->addProperty('additional_information', 'string');
                $def->save();

                $def = $this->createEntityType('syllabus_schedules_schedules', $this->getDataSource('Syllabus_Schedules_Schedule'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('schedules_id', 'int');
                $def->addProperty('column1', 'string');
                $def->addProperty('column2', 'string');
                $def->addProperty('column3', 'string');
                $def->addProperty('column4', 'string');
                $def->addProperty('sort_order', 'int');
                $def->save();
               
               break;

            case 1:
                // $def = $this->alterEntityType('syllabus_schedules_schedules', 
                //     $this->getDataSource('Syllabus_Schedules_Schedule')
                // );
                // $def->addProperty('date_field', 'datetime');
                // $def->save();
                break;

            case 2:
                $def = $this->alterEntityType('syllabus_schedules_schedules', 
                    $this->getDataSource('Syllabus_Schedules_Schedule')
                );
                $def->addProperty('date_field', 'datetime');
                $def->save();

                break;
        }
    }
}