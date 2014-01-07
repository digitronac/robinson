<?php
// autoload
include __DIR__ . '/../vendor/autoload.php';

// zf style env
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?: 'production'));
use Phalcon\Mvc\Application;

error_reporting(E_ALL);

define('APPLICATION_PATH', realpath(__DIR__ . '/../apps'));

/**
 * Include services
 */
require __DIR__ . '/../config/services.php';

if(APPLICATION_ENV !== 'production')
{
    ini_set('display_errors', 1);
    (new \Phalcon\Debug())->listen();
}

register_shutdown_function(function() use ($di)
{
    $lastError = error_get_last();
    if(!isset($lastError['message']) || !$di->has('log'))
    {
        return;
    }
    $di->getShared('log')->error($lastError);
});
/**
 * Handle the request
 */
$application = new Application();

/**
 * Assign the DI
 */
$application->setDI($di);

/**
 * Include modules
 */
require __DIR__ . '/../config/modules.php';


echo $application->handle()->getContent();


