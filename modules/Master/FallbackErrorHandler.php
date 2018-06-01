<?php

/**
 * Syllabus application error handler for errors that
 * are not caught elsewhere. There are three major causes of this error:
 * 
 * 1. An error is thrown that does not have an error handler for it. Many of
 *    the common errors (404, etc.) should already have error handlers for
 *    them, but it's possible you throw another error.
 * 2. A controller (or helper) throws an exception that should be handled by
 *    your code at a higher level. For example, if a library throws an
 *    exception to indicate validation errors, you should catch this in your
 *    controller and display an error back to the user, instead of letting
 *    the exception bubble up to this point.
 * 3. A resource that should be available is not. For example, we cannot
 *    connect to the database. In this case, this page is probably the most
 *    appropriate thing we can show the user.
 * 
 * @author      Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Master_FallbackErrorHandler extends Syllabus_Master_ErrorHandler
{
    public static function getErrorClassList () { return array(Bss_Routing_ErrorManager::UNHANDLED_ERROR_CLASS); }
    
    protected function getTemplateFile () { return 'error-500.html.tpl'; }
    protected function getStatusCode () { return 500; }
    protected function getStatusMessage () { return 'Internal Server Error'; }
    
    protected function handleError ($error)
    {
        // Otherwise, fail whale for the user.
        parent::handleError($error);
        
        if ($this->getApplication()->runMode === Bss_Core_Application::RUN_MODE_DEBUG)
        {
            $this->template->xxx = true;
            
            if ($error->getExtraInfo() instanceof Exception)
            {
                // Unwrap the original exception.
                $error = $error->getExtraInfo();
            }
            
            $this->template->errorClass = get_class($error);
            $this->template->errorMessage = $error->getMessage();
        }
    }
}
