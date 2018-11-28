<?php

if (file_exists('../var/composer/vendor/autoload.php'))
{
	require_once '../var/composer/vendor/autoload.php';
}

require_once 'bss.init.php';
require_once 'bss/core/Application.php';
require_once 'bss/core/Request.php';
require_once 'bss/core/Response.php';


function Bss_App_Start ()
{
    $app = Bss_Core_Application::initApplication(BSS_APP_CONFIG_FILE);
    $app->initEnvironment();
    
    $app->request = $request = new Bss_Core_Request($app);
    $app->response = $response = new Bss_Core_Response($request);
    
    $app->frontController->dispatchRequest($request, $response);
}

Bss_App_Start();
