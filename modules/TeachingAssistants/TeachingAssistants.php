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
            $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
            foreach ($data['section']['real'] as $id => $ta)
            {
                if ($this->isNotWhiteSpaceOnly($ta, 'name'))
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
                    $obj->absorbData($ta);
                    $obj->name = isset($ta['name']) ? strip_tags(trim($ta['name'])) : '';
                    $obj->email = isset($ta['email']) ? strip_tags(trim($ta['email'])) : '';
                    $obj->additionalInformation = $htmlSanitizer->sanitize(trim($ta['additionalInformation']));
                    $obj->teaching_assistants_id = $this->id;
                    $obj->save();
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
