<?php

/**
 * Handle upgrading accounts.
 * 
 * @author      Daniel A. Koepke (dkoepke@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_AuthN_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {
        switch ($fromVersion)
        {
            case 0:
                $this->requireModule('bss:core:authZ', 1);
                
                $this->useDataSource('Bss_AuthN_Account');
                
                // Entity for roles.
                $def = $this->createEntityType('syllabus_authn_roles', 'Syllabus_AuthN_Role');
                $def->addProperty('id', 'int', array('sequence' => true, 'primaryKey' => true));
                $def->addProperty('name', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('is_system_role', 'bool');
                $def->save();
                
                // M:N mapping for accounts <=> roles
                $def = $this->createEntityType('syllabus_authn_account_roles', 'Syllabus_AuthN_Role');
                $def->addProperty('account_id', 'int', array('primaryKey' => true));
                $def->addProperty('role_id', 'int', array('primaryKey' => true));
                $def->addForeignKey('bss_authn_accounts', array('account_id' => 'id'));
                $def->addForeignKey('syllabus_authn_roles', array('role_id' => 'id'));
                $def->addIndex('account_id');
                $def->save();
                
                // Create access levels entity.
                $def = $this->createEntityType('syllabus_authn_access_levels', 'Syllabus_AuthN_AccessLevel');
                $def->addProperty('id', 'int', array('sequence' => true, 'primaryKey' => true));
                $def->addProperty('name', 'string');
                $def->addProperty('description', 'string');
                $def->save();
                
                $this->useDataSource('Syllabus_AuthN_Role');

                $roleIdMap = $this->insertRecords('syllabus_authn_roles',
                    array(
                        array('name' => 'Administrator', 'description' => 'Has every possible permission.', 'is_system_role' => true),
                        array('name' => 'Faculty', 'description' => 'Faculty member', 'is_system_role' => true),
                        array('name' => 'Lecturer', 'description' => 'Lecturer member', 'is_system_role' => true),
                    ),
                    array(
                        'idList' => array('id')
                    )
                );
                
                $levelIdMap = $this->insertRecords('syllabus_authn_access_levels',
                    array(
                        array('name' => 'Private', 'description' => 'Only visible to those who have explicitly been granted access. <div class="detail">Good for tightly controlling access.</div>'),
                        array('name' => 'SFSU', 'description' => 'Only visible to those who are SFSU people.'),
                    ),
                    array(
                        'idList' => array('id')
                    )
                );
                
                break;
        }
    }
}
