<?php

/**
 * ImportableSections
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_ImportableSection extends Bss_ActiveRecord_Base
{

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_importable_sections',
            '__pk' => ['id'],
            
            'id' => 'int',
            'title' => 'string',
            'organizationId' => ['string', 'nativeName' => 'organization_id'],
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],
            'importable' => 'bool',
            'sortOrder' => ['int', 'nativeName' => 'sort_order'],
            'externalKey' => ['string', 'nativeName' => 'external_key'],

            'section' => ['1:1', 'to' => 'Syllabus_Syllabus_Section', 'keyMap' => ['section_id' => 'id']],
        ];
    }

    public function getOrganization ()
    {
        list($type, $id) = explode('/', $this->organizationId);
        return $this->getSchema('Syllabus_Organizations_Group')->get($id);
    }

    public function getType ($plural=false)
    {
        $len = strlen($this->externalKey);
        $type = substr($this->externalKey, 0, $len - 3);

        if ($plural)
        {
            return ucfirst($type);
        }
        else
        {
            $ext = $this->section->latestVersion->getExtensionByName($type);
            return $ext ? $ext->getDisplayName() : ucfirst($type);
        }
    }

    public function getSectionExtension ()
    {
        return $this->section->latestVersion->getExtensionByName(lcfirst($this->getType(true)));
    }
}
