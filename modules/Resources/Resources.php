<?php

/**
 * Resource section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Resources_Resources extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_resources',
            '__pk' => ['id'],
            
            'id' => 'int',
            'additionalInformation' => ['string', 'nativeName' => 'additional_information'],

            'resources' => [
                '1:N', 'to' => 'Syllabus_Resources_Resource', 'reverseOf' => 'parent', 'orderBy' => ['+sortOrder']
            ],
        ];
    }

    public function getResources ()
    {
        $resources = [];
        foreach ($this->_fetch('resources') as $obj)
        {
            $resources[] = $obj;
        }    
        return !empty($resources) ? $resources : null;
    }

    public function processEdit ($request) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';

        if (isset($data['section']) && isset($data['section']['real']))
        {
            if ($this->isNotWhiteSpaceOnly($data['section']['real'], 'additionalInformation'))
            {
                $this->additionalInformation = $data['section']['real']['additionalInformation'];
            }
            unset($data['section']['real']['additionalInformation']);
            $this->save();

            $resources = $this->getSchema('Syllabus_Resources_Resource');
            $campusResources = $this->getSchema('Syllabus_Syllabus_CampusResource');

            foreach ($data['section']['real'] as $id => $resource)
            {
                if ($id === 'campusResources')
                {
                	$existingResources = [];
                	foreach ($data['section']['real'] as $otherId => $otherResource)
                	{
                		if (is_numeric($otherId))
                		{
                			$existingResources[] = $otherResource;
                		}
                	}
                	$sortCounter = count($existingResources);
                	foreach ($resource as $campusResourceId)
                	{
                		$campusResource = $campusResources->get($campusResourceId);
                		if ($campusResource->inDatasource)
                		{
                			$sortCounter++;
                			$obj = $resources->createInstance();
                			$resourceData = $campusResource->getData();
                			unset($resourceData['id']);
                			unset($resourceData['image']);
                			$obj->absorbData($resourceData);
                			$obj->campusResourcesId = $campusResource->id;
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
                	unset($data['section']['real']['campusResources']);
                }
                elseif ($this->isNotWhiteSpaceOnly($resource, 'title') || (isset($resource['isCustom']) && $resource['isCustom'] === 'false'))
                {
                    $obj = (!is_numeric($id)) ? $resources->createInstance() : $resources->get($id);
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
                        $obj->absorbData($resource);
                        $obj->resources_id = $this->id;
                        $obj->isCustom = (isset($resource['isCustom']) && ($resource['isCustom']==='false')) ? false : true;
                        $obj->save();
                    }                   	
                }
                else
                {
                    $errorMsg = 'Any resources with empty descriptions were not saved.';
                }
            }
        }

        return $errorMsg;
    }
}
