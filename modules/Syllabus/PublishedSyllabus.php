<?php

/**
 * PublishedSyllabus active record.
 *
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_PublishedSyllabus extends Bss_ActiveRecord_Base
{
    public static function SchemaInfo ()
    {
        return array(
            '__type' => 'syllabus_syllabus',
            '__pk' => array('id'),
            
            'id' => 'int',
            'syllabusId' => array('int', 'nativeName' => 'syllabus_id'),
            'publishedDate' => array('datetime', 'nativeName' => 'published_date'),

            'syllabus' => array('1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => array('syllabus_id' => 'id')),

            'sections' => array('N:M',
                'to' => 'Syllabus_Syllabus_Section',
                'via' => 'syllabus_published_syllabus_section_map',
                'fromPrefix' => 'published_syllabus',
                'toPrefix' => 'section_id',
                'properties' => array('sort_order' => 'int', 'is_anchored' => 'bool'),
                'orderBy' => array('+_map.sort_order')
            ),         
        );
    }

    // returns the latest version of the Syllabus with this id
    public function getSyllabus ()
    {
        return $this->syllabus->getLatestVersion();
    }

    public function getSections ()
    {
        $sections = [];
        foreach ($this->sections->asArray() as $section)
        {
            $sections[] = $section->getLatestVersion();
        }

        return $sections;
    }

    // make sure version is the latest for this syllabus
    // and/or only one version exists for the referenced syllabus
    protected function beforeSave ()
    {
        parent::beforeSave();
    }
}