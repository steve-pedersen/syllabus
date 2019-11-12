<?php

/**
 * Policy section type active record. Container for Policy.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Policies_Policies extends Bss_ActiveRecord_Base
{
    use IsNotWhiteSpaceOnly;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_policies',
            '__pk' => ['id'],
            
            'id' => 'int',      

            'policies' => ['1:N', 'to' => 'Syllabus_Policies_Policy', 'reverseOf' => 'policiesSection', 'orderBy' => ['+sortOrder']],
        ];
    }

    public function getObjectives ()
    {
        $policies = [];
        foreach ($this->_fetch('policies') as $obj)
        {
            $policies[] = $obj;
        }    
        return !empty($policies) ? $policies : null;
    }

    public function processEdit ($request, $data=null) 
    {
        $data = $request->getPostParameters();
        $errorMsg = '';
        if (isset($data['section']) && isset($data['section']['real']))
        {
            $this->save();
            $schema = $this->getSchema('Syllabus_Policies_Policy');
            $htmlSanitizer = new Bss_RichText_HtmlSanitizer();
            foreach ($data['section']['real'] as $id => $policy)
            {
                if ($this->isNotWhiteSpaceOnly($policy, 'description'))
                {
                    $save = true;
                    $obj = $schema->createInstance();
                    if ($save)
                    {
                        $obj->absorbData($policy);
                        $obj->title = isset($policy['title']) ? strip_tags(trim($policy['title'])) : '';
                        $obj->description = $htmlSanitizer->sanitize(trim($policy['description']));
                        $obj->policies_id = $this->id;
                        $obj->save();
                    }   
                }
                else
                {
                    $errorMsg = 'Any policies with empty descriptions were not saved.';
                }
            }
        }

        return $errorMsg;
    }

    public function copyImportables ($resolvedImportable)
    {
        $ignoredProperties = ['sortOrder', 'id', 'policies'];
        $sortOrder = count($this->policies);
        $imported = [];

        foreach ($resolvedImportable->policies as $policy)
        {
            $deriv = $this->getSchema('Syllabus_Policies_Policy')->createInstance();
            foreach ($policy->getData() as $key => $val)
            {
                if (!in_array($key, $ignoredProperties))
                {
                    $deriv->$key = $val;
                }
                $deriv->sortOrder = $sortOrder;
                $sortOrder++;
            }
            $deriv->policies_id = $this->id;
            $deriv->save();
            $imported[] = $deriv;
        }

        return $imported;
    }
}
