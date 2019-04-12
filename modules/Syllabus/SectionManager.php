<?php

/**
 * Utility class for managing syllabi and sections.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
class Syllabus_Syllabus_Manager
{
    private $app;
    private $syllabus;
    
    public function __construct (Bss_Core_Application $app, Bss_ActiveRecord_Base $syllabus)
    {
        $this->app = $app;
        $this->syllabus = $syllabus;
    }

    public function insertSection ($section, $to) 
    {

    }

    public function saveSyllabus () 
    {

    }

    public function schema ($recordClass)
    {
        return $this->app->schemaManager->getSchema($recordClass);
    }
}