<?php

/**
 */
class Syllabus_Files_TaskDefinitionProvider extends Bss_AuthZ_TaskDefinitionProvider
{
    public function getTaskDefinitions ()
    {
        return array(
            'file download' => 'download the file',
        );
    }
}
