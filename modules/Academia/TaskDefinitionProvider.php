<?php

/**
 */
class Syllabus_Academia_TaskDefinitionProvider extends Bss_AuthZ_TaskDefinitionProvider
{
    public function getTaskDefinitions ()
    {
        return array(
            'entity member' => "Ability to view an academic entity's content. Read only.",
            'entity contributor' => "Ability to contribute content to an academic entity. Read & Write only.",
            'entity moderator' => "Ability to moderate content within an academic entity. Read, Write, Edit, Delete.",
            'entity admin' => "Ability to administer content, settings, and users within an academic entity. Read, Write, Edit, Delete, Edit Settings, Manage Users.",
        );
    }
}