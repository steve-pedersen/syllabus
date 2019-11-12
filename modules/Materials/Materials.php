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

    public function processEdit ($request, $data=null) 
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
                    $save = true;
                    $obj = $schema->createInstance();
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

    public function copyImportables ($resolvedImportable)
    {
        $ignoredProperties = ['sortOrder', 'id', 'materials'];
        $sortOrder = count($this->materials);
        $imported = [];

        foreach ($resolvedImportable->materials as $material)
        {
            $deriv = $this->getSchema('Syllabus_Materials_Material')->createInstance();
            foreach ($material->getData() as $key => $val)
            {
                if (!in_array($key, $ignoredProperties))
                {
                    $deriv->$key = $val;
                }
                $deriv->sortOrder = $sortOrder;
                $sortOrder++;
            }
            $deriv->materials_id = $this->id;
            $deriv->save();
            $imported[] = $deriv;
        }

        return $imported;
    }
}
