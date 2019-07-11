<?php

/**
 * TeachingAssistants section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_TeachingAssistants_TeachingAssistants extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_teaching_assistants',
            '__pk' => ['id'],
            
            'id' => 'int',

            'teachingAssistants' => ['1:N', 'to' => 'Syllabus_TeachingAssistants_TeachingAssistant', 'reverseOf' => 'teachingAssistantsSection', 'orderBy' => ['+sortOrder']],
        ];
    }

    public function getTeachingAssistants ()
    {
        $teachingAssistants = [];
        foreach ($this->_fetch('teachingAssistants') as $obj)
        {
            $teachingAssistants[] = $obj;
        }    
        return !empty($teachingAssistants) ? $teachingAssistants : null;
    }

    public function processEdit ($request) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';
        if (isset($data['section']) && isset($data['section']['real']))
        {
            $this->save();
            $schema = $this->getSchema('Syllabus_TeachingAssistants_TeachingAssistant');
            foreach ($data['section']['real'] as $id => $teachingAssistant)
            {
                if ($this->isNotWhiteSpaceOnly($teachingAssistant, 'name'))
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
                        $obj->absorbData($teachingAssistant);
                        $obj->teaching_assistants_id = $this->id;
                        $obj->save();
                    }   
                }
                else
                {
                    $errorMsg = 'Any Teaching Assistants with empty descriptions were not saved.';
                }
            }
        }

        return $errorMsg;
    }
}
