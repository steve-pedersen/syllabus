<?php

/**
 * Schedules section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Schedules_Schedules extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_schedules',
            '__pk' => ['id'],
            
            'id' => 'int',
            'columns' => 'int',
            'header1' => 'string',
            'header2' => 'string',
            'header3' => 'string',
            'header4' => 'string',
            'additionalInformation' => ['string', 'nativeName' => 'additional_information'],

            'schedules' => ['1:N', 'to' => 'Syllabus_Schedules_Schedule', 'reverseOf' => 'parent', 'orderBy' => ['+sortOrder']],
        ];
    }

    public function getDefaults ()
    {
        $defaults = $this->getSchema()->createInstance();
        $defaults->columns = 3;
        $defaults->header1 = 'Date';
        $defaults->header2 = 'Topics & Activities';
        $defaults->header3 = 'Deliverables';

        return $defaults;
    }

    public function getSchedules ()
    {
        $schedules = [];
        foreach ($this->_fetch('schedules') as $obj)
        {
            $schedules[] = $obj;
        }    
        return !empty($schedules) ? $schedules : null;
    }

    public function processEdit ($request, $data=null) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';
        // echo "<pre>"; var_dump($data['section']['real']); die;
        if (isset($data['section']) && isset($data['section']['real']))
        {
            $data = $data['section']['real'];
            $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
            $this->absorbData($data);
            $this->header1 = isset($data['header1']) ? strip_tags(trim($data['header1'])) : '';
            $this->header2 = isset($data['header2']) ? strip_tags(trim($data['header2'])) : '';
            $this->header3 = isset($data['header3']) ? strip_tags(trim($data['header3'])) : '';
            $this->header4 = isset($data['header4']) ? strip_tags(trim($data['header4'])) : '';
            if (isset($data['additionalInformation']) && $data['additionalInformation'] !== '')
            {
                $this->additionalInformation = $htmlSanitizer->sanitize(trim($data['additionalInformation']), $options);
                unset($data['additionalInformation']);
            }
            $this->save();

            unset($data['columns']);
            unset($data['header1']);
            unset($data['header2']);
            unset($data['header3']);
            unset($data['header4']);

            $schema = $this->getSchema('Syllabus_Schedules_Schedule');
            foreach ($data as $id => $schedule)
            {
                if ($this->isNotWhiteSpaceOnly($schedule, 'column1') || $this->isNotWhiteSpaceOnly($schedule, 'column2'))
                {
                    $save = true;
                    $obj = $schema->createInstance();
                    if ($save)
                    {
                        $obj->absorbData($schedule);
                        if (strpos($schedule['column1'], 'data-timestamp="') !== false)
                        {
                            $p0 = strpos($schedule['column1'], '<span data-timestamp="');
                            $p1 = $p0 + 22;
                            $p2 = strpos($schedule['column1'], '"', $p1+1);
                            $dateField = substr($schedule['column1'], $p1, $p2-$p1);
                            $obj->dateField = new DateTime($dateField) ?? null;
                        }
                        $obj->column1 = $htmlSanitizer->sanitize(trim($schedule['column1']));
                        $obj->column2 = $htmlSanitizer->sanitize(trim($schedule['column2']));
                        $obj->column3 = $htmlSanitizer->sanitize(trim($schedule['column3']));
                        $obj->column4 = $htmlSanitizer->sanitize(trim($schedule['column4']));
                        $obj->schedules_id = $this->id;
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
        $ignoredProperties = ['sortOrder', 'id', 'schedules', 'additionalInformation'];
        $containerProperties = ['columns', 'header1', 'header2', 'header3', 'header4'];
        $sortOrder = count($this->grades);
        $imported = [];

        if (!isset($this->id)) $this->save();
        foreach ($containerProperties as $prop)
        {
            if (!isset($this->$prop) || $this->$prop === '' || ($prop === 'columns' && $this->$prop !== $resolvedImportable->$prop))
            {
                $this->$prop = $resolvedImportable->$prop;
            }           
        }
        if (!isset($this->id)) $this->save();

        foreach ($resolvedImportable->schedules as $schedule)
        {
            $deriv = $this->getSchema('Syllabus_Schedules_Schedule')->createInstance();
            foreach ($schedule->getData() as $key => $val)
            {
                if (!in_array($key, $ignoredProperties))
                {
                    $deriv->$key = $val;
                }
                $deriv->sortOrder = $sortOrder;
                $sortOrder++;
            }
            $deriv->schedules_id = $this->id;
            $deriv->save();
            $imported[] = $deriv;
        }

        return $imported;
    }
}
