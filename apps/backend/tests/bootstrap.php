<?php
use Phalcon\DI,
    Phalcon\DI\FactoryDefault;

// autoload
include __DIR__ . '/../../../vendor/autoload.php';


ini_set('display_errors',1);
error_reporting(E_ALL);

define('APPLICATION_PATH', realpath(__DIR__ . '/../../../apps'));

$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
        'Robinson\Backend\Controllers' => APPLICATION_PATH . '/backend/controllers/',
        'Robinson\Backend\Models' => APPLICATION_PATH . '/backend/models/',
        'Robinson\Backend\Plugin' => APPLICATION_PATH . '/backend/plugins/',
        'Robinson\Backend\Validator' => APPLICATION_PATH . '/backend/validators/',
        'Phalcon\Test\Fixtures' => APPLICATION_PATH . '/backend/tests/fixtures/',
));

$loader->register();
include_once APPLICATION_PATH . '/backend/tests/controllers/BaseTestController.php';
