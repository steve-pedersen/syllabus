<?php

class Syllabus_Files_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_files', $this->getDataSource('Syllabus_Files_File'));
                $def->addProperty('id', 'int', array('sequence' => true, 'primaryKey' => true));
                $def->addProperty('remote_name', 'string');
                $def->addProperty('local_name', 'string');
                $def->addProperty('content_type', 'string');
                $def->addProperty('content_length', 'int');
                $def->addProperty('hash', 'string');
                $def->addProperty('temporary', 'bool');
                $def->addProperty('title', 'string');
                $def->addProperty('uploaded_date', 'datetime');
                $def->addProperty('uploaded_by_id', 'int');
                $def->addForeignKey('bss_authn_accounts', array('uploaded_by_id' => 'id'));
                $def->save();
                
                break;
        }
    }
}