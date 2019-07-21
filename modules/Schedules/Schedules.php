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

    public function processEdit ($request) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';
        echo "<pre>"; var_dump('fix bug: changing order when editing new rows deactivcates the ckeditors. test this on other sections that have ckeditors too.'); die;
        if (isset($data['section']) && isset($data['section']['real']))
        {
            $data = $data['section']['real'];
            $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
            $this->absorbData($data);
            $this->header1 = isset($data['header1']) ? strip_tags(trim($data['header1'])) : '';
            $this->header2 = isset($data['header2']) ? strip_tags(trim($data['header2'])) : '';
            $this->header3 = isset($data['header3']) ? strip_tags(trim($data['header3'])) : '';
            $this->header4 = isset($data['header4']) ? strip_tags(trim($data['header4'])) : '';
            $this->additionalInformation = $htmlSanitizer->sanitize(trim($data['additionalInformation']));
            $this->save();

            unset($data['columns']);
            unset($data['header1']);
            unset($data['header2']);
            unset($data['header3']);
            unset($data['header4']);
            unset($data['additionalInformation']);

            $schema = $this->getSchema('Syllabus_Schedules_Schedule');
            foreach ($data as $id => $schedule)
            {
                if ($this->isNotWhiteSpaceOnly($schedule, 'column1') || $this->isNotWhiteSpaceOnly($schedule, 'column2'))
                {
                    $obj = (!is_numeric($id)) ? $schema->createInstance() : $schema->get($id);
                    $save = true;
                    if ($obj->inDatasource)
                    {
                        if ($obj->id != $id)
                        {
                            $save = false;
                        }
                    }
                    if ($save)
                    {
                        $obj->absorbData($schedule);
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
}
