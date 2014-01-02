<?php
use Phalcon\DI,
    Phalcon\DI\FactoryDefault;

// autoload
include __DIR__ . '/../vendor/autoload.php';


ini_set('display_errors',1);
error_reporting(E_ALL);

define('APPLICATION_PATH', realpath(__DIR__ . '/../apps'));
include APPLICATION_PATH . '/../tests/backend/controllers/BaseTestController.php';
