<?php

/**
 * Section active record base implementation
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_Section extends Bss_ActiveRecord_Base implements Syllabus_Syllabus_ISection
{
    private $_sectionTypes;
    private $_latestVersion;

    public static function SchemaInfo ()
    {
        return [
            '__type' => 'syllabus_sections',
            '__pk' => ['id'],
            
            'id' => 'int',       
            'createdById' => ['int', 'nativeName' => 'created_by_id'],
            'createdDate' => ['datetime', 'nativeName' => 'created_date'],
            'modifiedDate' => ['datetime', 'nativeName' => 'modified_date'],

            'createdBy' => ['1:1', 'to' => 'Bss_AuthN_Account', 'keyMap' => ['created_by_id' => 'id']],
            'versions' => ['1:N', 'to' => 'Syllabus_Syllabus_SectionVersion', 'reverseOf' => 'section', 'orderBy' => ['+createdDate']],
        ];
    }

    public function getLatestVersion ()
    {
        return array_pop($this->versions->asArray());
    }

    /**
     * Returns the class names for all the real Section ActiveRecord types.
     */
    public function getSectionTypes ()
    {
        if (empty($this->_sectionTypes))
        {
            $extensions = $this->getSchema('Syllabus_Syllabus_SectionVersion')->getExtensions();
            foreach ($extensions as $ext)
            {
                $this->_sectionTypes[] = $ext->getRecordClass();
            }
        }

        return $this->_sectionTypes;
    }

    // TODO: add param for specific versions
    public function getTitle ()
    {
        return $this->latestVersion->title;
    }

    // TODO: add param for specific versions
    public function getDescription ()
    {
        return $this->latestVersion->description;
    }
}
