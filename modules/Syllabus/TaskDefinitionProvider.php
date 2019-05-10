<?php

/**
 */
class Syllabus_Syllabus_TaskDefinitionProvider extends Bss_AuthZ_TaskDefinitionProvider
{
    public function getTaskDefinitions ()
    {
        $tasks =  [
            'syllabus list' => 'see in a list - syllabus',
            'syllabus view' => 'view the contents of syllabus',
            'syllabus edit' => 'edit the information for syllabus',
            'syllabus share' => 'share the syllabus with others',
            'syllabus delete' => 'delete the syllabus',
            'section list' => 'see in a list - section',
            'section view' => 'view the contents of section',
            'section edit' => 'edit the  section',
            'section share' => 'share the section with others',
            'section delete' => 'delete the section',
            'section make required' => 'make a section required for this syllabus (template) and editable by you only',
        ];

        $extensions = $this->getApplication()->moduleManager->getExtensions('at:syllabus:syllabus/sectionExtensions');
        foreach ($extensions as $extension) {
            $tasks = array_merge($tasks, $extension->getSectionTasks());
        }
        return $tasks;
    }
}
