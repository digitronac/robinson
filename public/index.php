<?php
// autoload
include __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


use Phalcon\Mvc\Application;

(new \Phalcon\Debug())->listen();

define('APPLICATION_PATH', realpath(__DIR__ . '/../apps'));

/**
 * Include services
 */
require __DIR__ . '/../config/services.php';

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
