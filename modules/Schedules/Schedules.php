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

        if (isset($data['section']) && isset($data['section']['real']))
        {
            $this->absorbData($data['section']['real']);
            $this->save();

            unset($data['section']['real']['columns']);
            unset($data['section']['real']['header1']);
            unset($data['section']['real']['header2']);
            unset($data['section']['real']['header3']);
            unset($data['section']['real']['header4']);
            unset($data['section']['real']['additionalInformation']);

            $schema = $this->getSchema('Syllabus_Schedules_Schedule');
            foreach ($data['section']['real'] as $id => $schedule)
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
