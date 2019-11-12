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

    public function processEdit ($request, $data=null) 
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
                if ($id === 'importable')
                {
                    $existingItems = [];
                    foreach ($data as $otherId => $otherResource)
                    {
                        if (is_numeric($otherId))
                        {
                            $existingItems[] = $otherResource;
                        }
                    }
                    $sortCounter = count($existingItems);
                    foreach ($resource as $campusResourceId)
                    {
                        $campusResource = $campusResources->get($campusResourceId);
                        if ($campusResource->inDatasource)
                        {
                            $sortCounter++;
                            $obj = $resources->createInstance();
                            $obj = $this->copyCampusResource($campusResource, $obj);
                            $obj->resources_id = $this->id;
                            $obj->sortOrder = $sortCounter;
                            $obj->isCustom = false;
                            $obj->save();
                        }
                        else
                        {
                            $errorMsg = 'Invalid Campus Resource id was submitted.';
                        }
                    }
                    unset($data['campusResources']);
                }
                elseif ($this->isNotWhiteSpaceOnly($objective, 'description'))
                {
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

    public function copyImportables ($resolvedImportable)
    {
        $ignoredProperties = ['sortOrder', 'id', 'objectives'];
        $sortOrder = count($this->objectives);
        $imported = [];

        foreach ($resolvedImportable->objectives as $objective)
        {
            $deriv = $this->getSchema('Syllabus_Objectives_Objective')->createInstance();
            foreach ($objective->getData() as $key => $val)
            {
                if (!in_array($key, $ignoredProperties))
                {
                    $deriv->$key = $val;
                }
                $deriv->sortOrder = $sortOrder;
                $sortOrder++;
            }
            $deriv->objectives_id = $this->id;
            $deriv->save();
            $imported[] = $deriv;
        }

        return $imported;
    }
}
