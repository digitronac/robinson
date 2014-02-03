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
        'Robinson\Frontend\Controllers' => APPLICATION_PATH . '/frontend/controllers/',
        'Robinson\Frontend\Models' => APPLICATION_PATH . '/frontend/models/',
        'Robinson\Backend\Plugin' => APPLICATION_PATH . '/frontend/plugins/',
        'Robinson\Backend\Validator' => APPLICATION_PATH . '/frontend/validators/',
        'Phalcon\Test\Fixtures' => APPLICATION_PATH . '/frontend/tests/fixtures/',
        'Robinson\Frontend\Tests\Validators' => APPLICATION_PATH . '/frontend/tests/validators/',
        'Robinson\Frontend\Tests\Filter' => APPLICATION_PATH . '/frontend/tests/filters/',
));

$loader->register();
require_once APPLICATION_PATH . '/frontend/tests/controllers/BaseTestController.php';
require_once APPLICATION_PATH . '/frontend/tests/models/BaseTestModel.php';
