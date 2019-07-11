<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_LearningOutcomes_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_learning_outcomes', $this->getDataSource('Syllabus_LearningOutcomes_LearningOutcomes'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('columns', 'int');
                $def->addProperty('header1', 'string');
                $def->addProperty('header2', 'string');
                $def->addProperty('header3', 'string');
                $def->addProperty('additional_information', 'string');
                $def->save();

                $def = $this->createEntityType('syllabus_learning_outcomes_learning_outcomes', $this->getDataSource('Syllabus_LearningOutcomes_LearningOutcome'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('learning_outcomes_id', 'int');
                $def->addProperty('column1', 'string');
                $def->addProperty('column2', 'string');
                $def->addProperty('column3', 'string');
                $def->addProperty('sort_order', 'int');
                $def->save();
               
               break;
        }
    }
}