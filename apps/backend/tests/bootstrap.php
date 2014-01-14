<?php
use Phalcon\DI,
    Phalcon\DI\FactoryDefault;

date_default_timezone_set('Europe/Belgrade');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// autoload
include __DIR__ . '/../../../vendor/autoload.php';

define('APPLICATION_PATH', realpath(__DIR__ . '/../../../apps'));
define('APPLICATION_ENV', 'testing');

$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
        'Robinson\Backend\Controllers' => APPLICATION_PATH . '/backend/controllers/',
        'Robinson\Backend\Models' => APPLICATION_PATH . '/backend/models/',
        'Robinson\Backend\Plugin' => APPLICATION_PATH . '/backend/plugins/',
        'Robinson\Backend\Validator' => APPLICATION_PATH . '/backend/validators/',
        'Phalcon\Test\Fixtures' => APPLICATION_PATH . '/backend/tests/fixtures/',
        'Robinson\Backend\Tests\Validators' => APPLICATION_PATH . '/backend/tests/validators/',
));

$loader->register();
require_once APPLICATION_PATH . '/backend/tests/controllers/BaseTestController.php';
require_once APPLICATION_PATH . '/backend/tests/models/BaseTestModel.php';
