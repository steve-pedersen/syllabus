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
            '__type' => 'syllabus_published_syllabus',
            '__pk' => ['id'],
            
            'id' => 'int',
            'syllabusId' => ['int', 'nativeName' => 'syllabus_id'],
            'publishedDate' => ['datetime', 'nativeName' => 'published_date'],
            'shareLevel' => ['string', 'nativeName' => 'share_level'],

            'syllabus' => ['1:1', 'to' => 'Syllabus_Syllabus_Syllabus', 'keyMap' => ['syllabus_id' => 'id']],        
        ];
    }

    // returns the latest version of the Syllabus with this id
    public function getSyllabus ()
    {
        return $this->_fetch('syllabus');
    }

    // make sure version is the latest for this syllabus
    // and/or only one version exists for the referenced syllabus
    protected function beforeSave ()
    {
        parent::beforeSave();
    }
}