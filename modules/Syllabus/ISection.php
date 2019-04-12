<?php

/**
 * Interface for Section type active records.
 * 
 * @author      Steve Pedersen (pedersen@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University.
 */
interface Syllabus_Syllabus_ISection
{
    /**
     * Determine which typeId is set on the Section active record
     * and fetch that section type.
     * 
     * @return ActiveRecord
     */
    // public function resolveSection ();

    /**
     * All section types (Schedule, Assignment, etc.) have title properties
     * 
     * @return string
     */
    public function getTitle ();

    /**
     * All section types (Schedule, Assignment, etc.) have description properties
     * 
     * @return string
     */
    public function getDescription ();


}