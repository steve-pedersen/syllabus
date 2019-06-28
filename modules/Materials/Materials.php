<?php

/**
 * Material section type active record.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Materials_Materials extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_materials',
            '__pk' => ['id'],
            
            'id' => 'int',
            'additionalInformation' => ['string', 'nativeName' => 'additional_information'],

            'materials' => ['1:N', 'to' => 'Syllabus_Materials_Material', 'reverseOf' => 'materialsSection', 'orderBy' => ['+sortOrder']],
        ];
    }

    public function getMaterials ()
    {
        $materials = [];
        foreach ($this->_fetch('materials') as $obj)
        {
            $materials[] = $obj;
        }    
        return !empty($materials) ? $materials : null;
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
            $schema = $this->getSchema('Syllabus_Materials_Material');
            foreach ($data['section']['real'] as $id => $material)
            {
                // TODO: Fix this for Material
                if ($this->isNotWhiteSpaceOnly($material, 'title'))
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
                        $obj->absorbData($material);
                        $obj->required = $material['required'] === 'true';
                        $obj->materials_id = $this->id;
                        $obj->save();
                    }   
                }
                else
                {
                    $errorMsg = 'Any materials with empty descriptions were not saved.';
                }
            }
        }

        return $errorMsg;
    }
}