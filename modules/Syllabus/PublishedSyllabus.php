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
        return [
            '__type' => 'syllabus_syllabus',
            '__pk' => ['id'],
            
            'id' => 'int',
            'syllabusId' => ['int', 'nativeName' => 'syllabus_id'],
            'publishedDate' => ['datetime', 'nativeName' => 'published_date'],
            'shareLevel' => ['string', 'nativeName' => 'share_level'],

            'syllabus' => ['1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => ['syllabus_id' => 'id']],

            // 'sections' => ['N:M',
            //     'to' => 'Syllabus_Syllabus_Section',
            //     'via' => 'syllabus_published_syllabus_section_map',
            //     'fromPrefix' => 'published_syllabus',
            //     'toPrefix' => 'section_id',
            //     'properties' =>['sort_order' => 'int', 'is_anchored' => 'bool'],
            //     'orderBy' =>['+_map.sort_order']
            // ],         
        ];
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