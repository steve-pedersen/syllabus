<?php

/**
 */
class Syllabus_Organizations_TaskDefinitionProvider extends Bss_AuthZ_TaskDefinitionProvider
{
    public function getTaskDefinitions ()
    {
        return array(
        	'view org' 					=> 'view organization',
        	'view org members' 		    => 'view organization members',
        	'view communications'		=> 'view communications from organization',
        	'create communications'		=> 'create and send communications to organization',
        	'view org templates' 		=> 'view organization templates',
        	'edit org templates'		=> 'edit organization templates',
        	'delete org templates'		=> 'delete organization templates',
        	'delete own org templates'	=> 'delete organization templates that have been created by you',
        	'manage submitted syllabi'	=> 'manage (view/approve/reject) submitted syllabi',
        	'comment org templates'		=> 'make comments on organization templates, such as help text',
        	'comment submitted syllabi'	=> 'make comments on submitted syllabi',
        	'manage org users'			=> 'manage organization users',
        	'view public repository'	=> 'view the organization public repository',
        	'view private repository'	=> 'view the organization private repository',
        	'manage repository'			=> 'manage the organization repositories',
        	'manage org'				=> 'manage the organization',
        );
    }
}