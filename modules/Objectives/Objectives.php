<?php

/**
 * Objective section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Objectives_Objectives extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_objectives',
            '__pk' => ['id'],
            
            'id' => 'int',

            'objectives' => ['1:N', 'to' => 'Syllabus_Objectives_Objective', 'reverseOf' => 'objectivesSection', 'orderBy' => ['+sortOrder']],
        ];
    }

    public function getObjectives ()
    {
        $objectives = [];
        foreach ($this->_fetch('objectives') as $obj)
        {
            $objectives[] = $obj;
        }    
        return !empty($objectives) ? $objectives : null;
    }

    public function processEdit ($request) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';
        if (isset($data['section']) && isset($data['section']['real']))
        {
            $this->save();
            $schema = $this->getSchema('Syllabus_Objectives_Objective');
            $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
            foreach ($data['section']['real'] as $id => $objective)
            {
                if ($this->isNotWhiteSpaceOnly($objective, 'description'))
                {
                    // $obj = (!is_numeric($id)) ? $schema->createInstance() : $schema->get($id);
                    // $save = true;
                    // if ($obj->inDatasource)
                    // {
                    //     if ($obj->objectives_id != $this->id)
                    //     {
                    //         $save = false;
                    //     }
                    // }
                    $save = true;
                    $obj = $schema->createInstance();
                    if ($save)
                    {
                        $obj->absorbData($objective);
                        $obj->title = isset($objective['title']) ? strip_tags(trim($objective['title'])) : '';
                        $obj->description = $htmlSanitizer->sanitize(trim($objective['description']));
                        $obj->objectives_id = $this->id;
                        $obj->save();
                    }   
                }
                else
                {
                    $errorMsg = 'Any objectives with empty descriptions were not saved.';
                }
            }
        }

        return $errorMsg;
    }
}
