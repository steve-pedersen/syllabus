<?php

/**
 * Syllabus application master template. Put functionality and members here that you
 * want to expose to everything that uses a template, including controllers
 * from the framework; controllers derived from your own master template; and
 * templates used in e-mails.
 * 
 * @author      Charles O'Sullivan (chsoney@sfsu.edu)
 * @copyright   Copyright &copy; San Francisco State University
 */
class Syllabus_Master_Template extends Bss_Master_Template
{
    // The following instance variables are available:
    // Bss_Core_Application $application
    // Bss_Core_IRequest $request
    // Bss_Core_IResponse $response
    // Bss_Core_Session $session
    // Bss_Master_Controller $handler
    
    protected function initTemplate ()
    {
        parent::initTemplate();
        
        // This controls the template that is used. Framework classes will also
        // use your master template.
        $this->setMasterTemplate(Bss_Core_PathUtils::path(dirname(__FILE__), 'resources', 'master.html.tpl'));
        
        // Register Smarty modifiers, etc. that you want to use:
        // $this->register_modifier('modifier', array($this, 'myModifier'));
    }
    
    protected function bindBeforeRender ()
    {
        parent::bindBeforeRender();
        
        // Setup template variables here. For a template used in a controller,
        // this happens AFTER your callback has run, immediately before the
        // template is rendered.
        
        // $this->assign('someTemplateVariable', 'A value');
    }
}
