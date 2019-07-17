<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Grades_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        $siteSettings = $this->getApplication()->siteSettings;

        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_grades', $this->getDataSource('Syllabus_Grades_Grades'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('columns', 'int');
                $def->addProperty('header1', 'string');
                $def->addProperty('header2', 'string');
                $def->addProperty('header3', 'string');
                $def->addProperty('additional_information', 'string');
                $def->save();

                $def = $this->createEntityType('syllabus_grades_grades', $this->getDataSource('Syllabus_Grades_Grade'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('grades_id', 'int');
                $def->addProperty('column1', 'string');
                $def->addProperty('column2', 'string');
                $def->addProperty('column3', 'string');
                $def->addProperty('sort_order', 'int');
                $def->save();

                $this->useDataSource('Syllabus_Grades_Grades');
                $defaultGradesMap = $this->insertRecords('syllabus_grades', [
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'columns' => 2,
                        'header1' => 'Letter Grade',
                        'header2' => 'Percentage Range'
                    ]
                ], ['idList' => ['id']]);
                $gradesId = $defaultGradesMap[0]['id'];

                $siteSettings->defineProperty('sections-grades-default-id', 'Grades section defaults', 'int');
                $siteSettings->setProperty('sections-grades-default-id', $gradesId);

                $this->useDataSource('Syllabus_Grades_Grade');
                $defaultGradeMap = $this->insertRecords('syllabus_grades_grades', [
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => 'A',
                        'column2' => '90 - 100%'
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => 'B',
                        'column2' => '80 - 89%'
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => 'C',
                        'column2' => '70 - 79%'
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => 'D',
                        'column2' => '60 - 69%'
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => 'F',
                        'column2' => '0 - 59%'
                    ]
                ], ['idList' => ['id']]);
               
               break;

            case 1:

                $this->useDataSource('Syllabus_Grades_Grades');
                $defaultGradesMap = $this->insertRecords('syllabus_grades', [
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'columns' => 2,
                        'header1' => 'Percentage Range',
                        'header2' => 'Letter Grade'
                    ]
                ], ['idList' => ['id']]);
                $gradesId = $defaultGradesMap[0]['id'];

                $siteSettings->defineProperty('sections-grades-default-id', 'Grades section defaults', 'int');
                $siteSettings->setProperty('sections-grades-default-id', $gradesId);

                $this->useDataSource('Syllabus_Grades_Grade');
                $defaultGradeMap = $this->insertRecords('syllabus_grades_grades', [
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '100.00% - 93.00%',
                        'column2' => 'A',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '92.99% - 90.00%',
                        'column2' => 'A-',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '89.99% - 87.00%',
                        'column2' => 'B+',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '86.99% - 83.00%',
                        'column2' => 'B',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '82.99% - 80.00%',
                        'column2' => 'B-',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '79.99% - 77.0%',
                        'column2' => 'C+',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '76.99% - 73.00%',
                        'column2' => 'C',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '72.99% - 70.00%',
                        'column2' => 'C-',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '69.99% - 67.00%',
                        'column2' => 'D+',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '66.99% - 63.00%',
                        'column2' => 'D',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '62.99% - 60.00%',
                        'column2' => 'D-',
                    ],
                    [
                        'id' => ['type' => 'int', 'sequence' => true],
                        'grades_id' => $gradesId,
                        'column1' => '59.99% - 0.00%',
                        'column2' => 'F',
                    ]
                ], ['idList' => ['id']]);
               
               break;
        }
    }
}