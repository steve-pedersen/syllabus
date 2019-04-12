<?php

/**
 * Upgrade this module.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_ModuleUpgradeHandler extends Bss_ActiveRecord_BaseModuleUpgradeHandler
{
    public function onModuleUpgrade ($fromVersion)
    {      
        switch ($fromVersion)
        {
            case 0:

                $def = $this->createEntityType('syllabus_syllabus', $this->getDataSource('Syllabus_Syllabus_Syllabus'));
                $def->addProperty('id', 'int', array('primaryKey' => true, 'sequence' => true));
                $def->addProperty('created_by_id', 'int');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->addIndex('created_by_id');
                $def->save();


                $def = $this->createEntityType('syllabus_syllabus_versions', $this->getDataSource('Syllabus_Syllabus_SyllabusVersion'));
                $def->addProperty('id', 'int', array('primaryKey' => true, 'sequence' => true));
                $def->addProperty('title', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('syllabus_id', 'int');
                $def->addProperty('created_date', 'datetime');
                $def->addIndex('syllabus_id');
                $def->save();


                $def = $this->createEntityType('syllabus_sections', $this->getDataSource('Syllabus_Syllabus_Section'));
                $def->addProperty('id', 'int', array('sequence' => true, 'primaryKey' => true));
                $def->addProperty('created_by_id', 'int');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
	            $def->addIndex('created_by_id');
                $def->save();


                $def = $this->createEntityType('syllabus_section_versions', $this->getDataSource('Syllabus_Syllabus_SectionVersion'));
                $def->addProperty('id', 'int', array('primaryKey' => true, 'sequence' => true));
                $def->addProperty('title', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('section_id', 'int');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('course_info_id', 'int');
                $def->addProperty('instructor_info_id', 'int');
                $def->addProperty('teaching_assistants_id', 'int');
                $def->addProperty('objectives_id', 'int');
                $def->addProperty('materials_id', 'int');
                $def->addProperty('grading_id', 'int');
                $def->addProperty('activities_id', 'int');
                $def->addProperty('schedule_id', 'int');
                $def->addProperty('learning_outcomes_id', 'int');
                $def->addProperty('resources_id', 'int');
                $def->addProperty('policies_id', 'int');
                $def->addIndex('section_id');
                $def->save();


                $def = $this->createEntityType('syllabus_subsections', $this->getDataSource('Syllabus_Syllabus_Subsection'));
                $def->addProperty('id', 'int', array('sequence' => true, 'primaryKey' => true));
                $def->addProperty('title', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('section_version_id', 'id');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->addProperty('is_anchored', 'bool');
                $def->addProperty('sort_order', 'int');
                $def->addIndex('section_version_id');
                $def->save();


                $def = $this->createEntityType('syllabus_syllabus_version_section_map', 'Syllabus_Syllabus_SyllabusVersion');
                $def->addProperty('syllabus_version_id', 'int', array('primaryKey' => true));
                $def->addProperty('section_version_id', 'int', array('primaryKey' => true));
                $def->addProperty('is_anchored', 'bool');
                $def->addProperty('sort_order', 'int');
                $def->addProperty('read_only', 'bool');
                $def->addProperty('log', 'string');
                $def->save();
         

                $def = $this->createEntityType('syllabus_published_syllabus', $this->getDataSource('Syllabus_Syllabus_PublishedSyllabus'));
                $def->addProperty('id', 'int', array('primaryKey' => true, 'sequence' => true));
                $def->addProperty('syllabus_id', 'int');
                $def->addProperty('published_date', 'datetime');
                $def->addIndex('syllabus_id');
                $def->save();


                $def = $this->createEntityType('syllabus_published_syllabus_section_map', 'Syllabus_Syllabus_PublishedSyllabus');
                $def->addProperty('published_id', 'int');
                $def->addProperty('section_id', 'int');
                $def->save();

                break;
        }
    }
}