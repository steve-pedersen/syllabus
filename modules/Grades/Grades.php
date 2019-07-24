<?php

/**
 * Grades section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Grades_Grades extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_grades',
            '__pk' => ['id'],
            
            'id' => 'int',
            'columns' => 'int',
            'header1' => 'string',
            'header2' => 'string',
            'header3' => 'string',
            'additionalInformation' => ['string', 'nativeName' => 'additional_information'],

            'grades' => ['1:N', 'to' => 'Syllabus_Grades_Grade', 'reverseOf' => 'parent', 'orderBy' => ['+sortOrder']],
        ];
    }

    public function getDefaults ()
    {
        $gradesId = $this->getApplication()->siteSettings->getProperty('sections-grades-default-id');
        $defaults = $this->getSchema()->get($gradesId);
        foreach ($defaults->grades as $i => $grade)
        {
            $grade->id = 'def-' . $i;
        }
        
        return $defaults;
    }

    public function getGrades ()
    {
        $grades = [];
        foreach ($this->_fetch('grades') as $obj)
        {
            $grades[] = $obj;
        }    
        return !empty($grades) ? $grades : null;
    }

    public function processEdit ($request) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';

        if (isset($data['section']) && isset($data['section']['real']))
        {
            $data = $data['section']['real'];
            $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
            $this->absorbData($data);
            $this->header1 = isset($data['header1']) ? strip_tags(trim($data['header1'])) : '';
            $this->header2 = isset($data['header2']) ? strip_tags(trim($data['header2'])) : '';
            $this->header3 = isset($data['header3']) ? strip_tags(trim($data['header3'])) : '';
            $this->additionalInformation = $htmlSanitizer->sanitize(trim($data['additionalInformation']));
            $this->save();

            unset($data['columns']);
            unset($data['header1']);
            unset($data['header2']);
            unset($data['header3']);
            unset($data['additionalInformation']);

            $schema = $this->getSchema('Syllabus_Grades_Grade');
            foreach ($data as $id => $grade)
            {
                if ($this->isNotWhiteSpaceOnly($grade, 'column1'))
                {
                    // $obj = (!is_numeric($id)) ? $schema->createInstance() : $schema->get($id);
                    // $save = true;
                    // if ($obj->inDatasource)
                    // {
                    //     if ($obj->id != $id)
                    //     {
                    //         $save = false;
                    //     }
                    // }

                    $save = true;
                    $obj = $schema->createInstance();
                    if ($save)
                    {
                        $obj->absorbData($grade);
                        $obj->column1 = $htmlSanitizer->sanitize(trim($grade['column1']));
                        $obj->column2 = $htmlSanitizer->sanitize(trim($grade['column2']));
                        $obj->column3 = $htmlSanitizer->sanitize(trim($grade['column3']));
                        $obj->grades_id = $this->id;
                        $obj->save();
                    }   
                }
                else
                {
                    $errorMsg = 'Any rows with empty cells were not saved.';
                }
            }
        }

        return $errorMsg;
    }


    // public function getDefaults ()
    // {
    //     $gradeSchema = $this->getSchema('Syllabus_Grades_Grade');
    //     $gradesSchema = $this->getSchema();
    //     $grades = $gradesSchema->createInstance();
    //     $grades->columns = 2;
    //     $grades->header1 = 'Letter Grade';
    //     $grades->header2 = 'Percentage Range';
    //     // $grades->header3 = 'Explanation';
    //     $grades->save();
    //     $row1 = $gradeSchema->createInstance();
    //     $row2 = $gradeSchema->createInstance();
    //     $row3 = $gradeSchema->createInstance();
    //     $row4 = $gradeSchema->createInstance();
    //     $row5 = $gradeSchema->createInstance();

    //     $row1->column1 = 'A';
    //     $row1->column2 = '90 - 100%';
    //     $row1->sortOrder = 1;
    //     $row1->grades_id = $grades->id;
    //     $row1->save();
    //     $row2->column1 = 'B';
    //     $row2->column2 = '80 - 89%';
    //     $row2->sortOrder = 2;
    //     $row2->grades_id = $grades->id;
    //     $row2->save();
    //     $row3->column1 = 'C';
    //     $row3->column2 = '70 - 79%';
    //     $row3->sortOrder = 3;
    //     $row3->grades_id = $grades->id;
    //     $row3->save();
    //     $row4->column1 = 'D';
    //     $row4->column2 = '60 - 69%';
    //     $row4->sortOrder = 4;
    //     $row4->grades_id = $grades->id;
    //     $row4->save();
    //     $row5->column1 = 'F';
    //     $row5->column2 = '0 - 59%';
    //     $row5->sortOrder = 5;
    //     $row5->grades_id = $grades->id;
    //     $row5->save();
        
    //     // // $collection = $this->_fetch('grades');
    //     // $grades->grades->add($row1);
    //     // $grades->grades->add($row2);
    //     // $grades->grades->add($row3);
    //     // $grades->grades->add($row4);
    //     // $grades->grades->add($row5);
    //     $rows = [];
    //     $rows[] = $row1;
    //     $rows[] = $row2;
    //     $rows[] = $row3;
    //     $rows[] = $row4;
    //     $rows[] = $row5;
    //     $grades->grades = $rows;


    //     return $grades;
    // }
}
