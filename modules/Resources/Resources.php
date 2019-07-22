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
        // echo "<pre>"; var_dump($data['section']['real']); die;
        if (isset($data['section']) && isset($data['section']['real']))
        {
            $data = $data['section']['real'];
            $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
            if ($this->isNotWhiteSpaceOnly($data, 'additionalInformation'))
            {
                $this->additionalInformation = $htmlSanitizer->sanitize(trim($data['additionalInformation']));
            }
            unset($data['additionalInformation']);
            $this->save();

            $resources = $this->getSchema('Syllabus_Resources_Resource');
            $campusResources = $this->getSchema('Syllabus_Syllabus_CampusResource');

            foreach ($data as $id => $resource)
            {
                if ($id === 'campusResources')
                {
                	$existingResources = [];
                	foreach ($data as $otherId => $otherResource)
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
                elseif (is_numeric($id) && isset($resource['isCustom']) && $resource['isCustom'] === 'false')
                {
                    $obj = $resources->createInstance();
                    $campusResource = $campusResources->get($resource['campusResourcesId']);
                    $obj = $this->copyCampusResource($campusResource, $obj);
                    $obj->resources_id = $this->id;
                    $obj->sortOrder = $resource['sortOrder'];
                    $obj->isCustom = false;
                    $obj->save();                   
                }
                elseif ($this->isNotWhiteSpaceOnly($resource, 'title'))
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
                        $obj->title = isset($resource['title']) ? strip_tags(trim($resource['title'])) : '';
                        $obj->url = isset($resource['url']) ? strip_tags(trim($resource['url'])) : '';
                        $obj->abbreviation = isset($resource['abbreviation']) ? strip_tags(trim($resource['abbreviation'])) : '';
                        $obj->description = $htmlSanitizer->sanitize(trim($resource['description']));
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

    public function copyCampusResource ($campusResource, $resource)
    {
        $resourceData = $campusResource->getData();
        unset($resourceData['id']);
        unset($resourceData['image']);
        $resource->absorbData($resourceData);
        $resource->campusResourcesId = $campusResource->id;

        return $resource;
    }
}
















