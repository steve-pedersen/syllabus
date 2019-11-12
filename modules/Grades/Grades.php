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

    public function processEdit ($request, $data=null) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';

        if (isset($data['section']) && isset($data['section']['real']))
        {
            $data = $data['section']['real'];
            $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
            $options['allowTags'] = ['hr'];
            $this->absorbData($data);
            $this->header1 = isset($data['header1']) ? strip_tags(trim($data['header1'])) : '';
            $this->header2 = isset($data['header2']) ? strip_tags(trim($data['header2'])) : '';
            $this->header3 = isset($data['header3']) ? strip_tags(trim($data['header3'])) : '';
            $this->additionalInformation = $htmlSanitizer->sanitize(trim($data['additionalInformation']), $options);
            $this->save();

            unset($data['columns']);
            unset($data['header1']);
            unset($data['header2']);
            unset($data['header3']);
            unset($data['additionalInformation']);

            $schema = $this->getSchema('Syllabus_Grades_Grade');
            foreach ($data as $id => $grade)
            {
                if ($this->isNotWhiteSpaceOnly($grade, 'column1') ||
                    $this->isNotWhiteSpaceOnly($grade, 'column2') ||
                    $this->isNotWhiteSpaceOnly($grade, 'column3'))
                {
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

    public function copyImportables ($resolvedImportable)
    {
        $ignoredProperties = ['sortOrder', 'id', 'grades'];
        $sortOrder = count($this->grades);
        $imported = [];

        foreach ($resolvedImportable->grades as $grade)
        {
            $deriv = $this->getSchema('Syllabus_Grades_Grade')->createInstance();
            foreach ($grade->getData() as $key => $val)
            {
                if (!in_array($key, $ignoredProperties))
                {
                    $deriv->$key = $val;
                }
                $deriv->sortOrder = $sortOrder;
                $sortOrder++;
            }
            $deriv->grades_id = $this->id;
            $deriv->save();
            $imported[] = $deriv;
        }

        return $imported;
    }
}
