<?php

class Syllabus_Syllabus_SyllabusErrorHandler extends Syllabus_Master_ErrorHandler
{
    public static function getErrorClassList () 
    { 
        return ['Syllabus_Syllabus_NoSyllabus'];
    }

    protected function getStatusCode () { return 404; }
    protected function getStatusMessage () { return 'Syllabus Not Found'; }
    protected function getTemplateFile () { return 'syllabusNotFound.html.tpl'; }
    
    protected function handleSyllabusNotFound ()
    {
    }

    protected function handleError ($error)
    {
        parent::handleError($error);
    }
}