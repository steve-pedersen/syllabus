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
        $siteSettings = $this->getApplication()->siteSettings;   
        switch ($fromVersion)
        {
            case 0:

                $siteSettings->defineProperty('university-template-user-id', 'Id of user authoring the university base template.', 'int');
                $siteSettings->defineProperty('university-template-id', 'Id of Syllabus ActiveRecord that is the university base template.', 'int');


                $def = $this->createEntityType('syllabus_syllabus', $this->getDataSource('Syllabus_Syllabus_Syllabus'));
                $def->addProperty('id', 'int', array('primaryKey' => true, 'sequence' => true));
                $def->addProperty('created_by_id', 'int');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->addProperty('template_authorization_id', 'string');
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
                $def->addProperty('course_id', 'int');
                $def->addProperty('instructor_id', 'int');
                $def->addProperty('teaching_assistants_id', 'int');
                $def->addProperty('objectives_id', 'int');
                $def->addProperty('materials_id', 'int');
                $def->addProperty('grading_id', 'int');
                $def->addProperty('activities_id', 'int');
                $def->addProperty('schedule_id', 'int');
                $def->addProperty('learning_outcomes_id', 'int');
                $def->addProperty('resources_id', 'int');
                $def->addProperty('policies_id', 'int');
                $def->addProperty('containers_id', 'int');
                $def->addProperty('container_group_id', 'int');
                $def->addIndex('section_id');
                $def->save();


                $def = $this->createEntityType('syllabus_subsections', $this->getDataSource('Syllabus_Syllabus_Subsection'));
                $def->addProperty('id', 'int', ['sequence' => true, 'primaryKey' => true]);
                $def->addProperty('title', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('section_version_id', 'id');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->addProperty('is_anchored', 'bool');
                $def->addProperty('sort_order', 'int');
                $def->addIndex('section_version_id');
                $def->save();


                $def = $this->createEntityType('syllabus_syllabus_version_section_version_map', 'Syllabus_Syllabus_SyllabusVersion');
                $def->addProperty('syllabus_version_id', 'int', ['primaryKey' => true]);
                $def->addProperty('section_version_id', 'int', ['primaryKey' => true]);
                $def->addProperty('is_anchored', 'bool');
                $def->addProperty('sort_order', 'int');
                $def->addProperty('read_only', 'bool');
                $def->addProperty('log', 'string');
                $def->addProperty('inherited', 'bool');
                $def->save();
         

                $def = $this->createEntityType('syllabus_published_syllabus', $this->getDataSource('Syllabus_Syllabus_PublishedSyllabus'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('syllabus_id', 'int');
                $def->addProperty('published_date', 'datetime');
                $def->addIndex('syllabus_id');
                $def->save();


                $def = $this->createEntityType('syllabus_published_syllabus_section_map', 'Syllabus_Syllabus_PublishedSyllabus');
                $def->addProperty('published_id', 'int');
                $def->addProperty('section_id', 'int');
                $def->save();


                $def = $this->createEntityType('syllabus_campus_resources', $this->getDataSource('Syllabus_Syllabus_CampusResource'));
                $def->addProperty('id', 'int', ['sequence' => true, 'primaryKey' => true]);
                $def->addProperty('title', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('abbreviation', 'string');
                $def->addProperty('url', 'string');
                $def->addProperty('image_id', 'int');
                $def->addProperty('sort_order', 'int');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('deleted', 'bool');
                $def->save();

                break;

            case 1:

                $def = $this->alterEntityType('syllabus_published_syllabus', $this->getDataSource('Syllabus_Syllabus_PublishedSyllabus'));
                $def->addProperty('share_level', 'string');
                $def->save();

                break;

            case 2:
                $def = $this->createEntityType('syllabus_shared_resources', $this->getDataSource('Syllabus_Syllabus_SharedResource'));
                $def->addProperty('id', 'int', ['sequence' => true, 'primaryKey' => true]);
                $def->addProperty('title', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('icon_class', 'string');
                $def->addProperty('url', 'string');
                $def->addProperty('file_id', 'int');
                $def->addProperty('sort_order', 'int');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('active', 'bool');
                $def->save();

                break;

            case 3:
                $def = $this->alterEntityType('syllabus_syllabus', $this->getDataSource('Syllabus_Syllabus_Syllabus'));
                $def->addProperty('token', 'string');
                $def->save();
                break;

            case 4:
                $def = $this->createEntityType('syllabus_roles', $this->getDataSource('Syllabus_Syllabus_Role'));
                $def->addProperty('id', 'int', ['sequence' => true, 'primaryKey' => true]);
                $def->addProperty('name', 'string');
                $def->addProperty('description', 'string');
                $def->addProperty('syllabus_id', 'int');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('expiry_date', 'datetime');
                $def->save();
                break;

            case 5:
                $def = $this->createEntityType('syllabus_submissions',
                    $this->getDataSource('Syllabus_Syllabus_Submission')
                );
                $def->addProperty('id', 'int', ['sequence' => true, 'primaryKey' => true]);
                $def->addProperty('campaign_id', 'int');
                $def->addProperty('syllabus_id', 'int');
                $def->addProperty('submitted_by_id', 'int');
                $def->addProperty('file_id', 'int');
                $def->addProperty('course_section_id', 'string');
                $def->addProperty('status', 'string');
                $def->addProperty('modified_date', 'datetime');
                $def->addProperty('submitted_date', 'datetime');
                $def->addProperty('approved_date', 'datetime');
                $def->addProperty('deleted', 'bool');
                $def->addProperty('log', 'string');
                $def->addProperty('feedback', 'string');
                $def->save();

                $def = $this->createEntityType('syllabus_submission_campaigns',
                    $this->getDataSource('Syllabus_Syllabus_SubmissionCampaign')
                );
                $def->addProperty('id', 'int', ['sequence' => true, 'primaryKey' => true]);
                $def->addProperty('description', 'string');
                $def->addProperty('semester_id', 'int');
                $def->addProperty('organization_authorization_id', 'string');
                $def->addProperty('modified_date', 'datetime');
                $def->addProperty('due_date', 'datetime');
                $def->addProperty('required', 'bool');
                $def->addProperty('log', 'string');
                $def->save();
                break;

            case 6:
                $def = $this->createEntityType('syllabus_importable_sections',
                    $this->getDataSource('Syllabus_Syllabus_ImportableSection')
                );
                $def->addProperty('id', 'int', ['sequence' => true, 'primaryKey' => true]);
                $def->addProperty('title', 'string');
                $def->addProperty('section_id', 'int');
                $def->addProperty('organization_id', 'string');
                $def->addProperty('importable', 'bool');
                $def->addProperty('sort_order', 'int');
                $def->addProperty('created_date', 'datetime');
                $def->addProperty('modified_date', 'datetime');
                $def->addProperty('external_key', 'string');
                $def->save();

                break;

            case 7:
                $def = $this->alterEntityType('syllabus_syllabus', $this->getDataSource('Syllabus_Syllabus_Syllabus'));
                $def->addProperty('file_id', 'int');
                $def->addProperty('course_section_id', 'string');
                $def->save();
                break;     

            case 8:
                $def = $this->createEntityType('syllabus_access_logs', $this->getDataSource('Syllabus_Syllabus_AccessLog'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('course_section_id', 'string');
                $def->addProperty('syllabus_id', 'int');
                $def->addProperty('account_id', 'int');
                $def->addProperty('access_date', 'datetime');
                $def->save();
                break; 

            case 9:
                $def = $this->createEntityType('syllabus_temporary_links', $this->getDataSource('Syllabus_Syllabus_TemporaryLink'));
                $def->addProperty('id', 'int', ['primaryKey' => true, 'sequence' => true]);
                $def->addProperty('course_section_id', 'string');
                $def->addProperty('syllabus_id', 'int');
                $def->addProperty('token', 'string');
                $def->addProperty('created_date', 'datetime');
                $def->save();
                break;   

            case 10:
                $def = $this->alterEntityType('syllabus_campus_resources', $this->getDataSource('Syllabus_Syllabus_CampusResource'));
                $def->addProperty('modified_date', 'datetime');
                $def->save();
                break;
        }
    }
}