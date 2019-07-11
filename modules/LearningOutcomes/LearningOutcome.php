<?php

/**
 * LearningOutcome section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_LearningOutcomes_LearningOutcome extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_learning_outcomes_learning_outcomes',
            '__pk' => ['id'],
            
            'id' => 'int',      
            'column1' => 'string',
            'column2' => 'string',
            'column3' => 'string',
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],

            'parent' => ['1:1', 'to' => 'Syllabus_LearningOutcomes_LearningOutcomes', 'keyMap' => ['learning_outcomes_id' => 'id']],
        ];
    }
}
