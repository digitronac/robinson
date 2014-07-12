<?php
// @codeCoverageIgnoreStart
//Using the CLI factory default services container
$di = new \Phalcon\DI\FactoryDefault\CLI();
include __DIR__ . '/../../../vendor/autoload.php';
// Define path to module directory
defined('MODULE_PATH')
|| define('MODULE_PATH', realpath(dirname(__FILE__) . '/../'));

// Define path to module directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../'));

// cli env
define('APPLICATION_ENV', 'cli');

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new \Phalcon\Loader();
$loader->registerDirs(
    array(
        MODULE_PATH . '/cli/tasks'
    )
);
$loader->register();

$config = new \Zend_Config_Ini(MODULE_PATH . '/config/application.ini', APPLICATION_ENV);
$config = new \Phalcon\Config($config->toArray());
if (is_file(MODULE_PATH . '/config/application.local.ini')) {
    $local = (new \Zend_Config_Ini(MODULE_PATH . '/config/application.local.ini', APPLICATION_ENV));
    $local = new \Phalcon\Config($local->toArray());
    $config->merge($local);
}

// services

$di->set('config', $config, true);

$di->set('db', function () use ($di) {
    $config = $di->get('config');
    $eventsManager = new \Phalcon\Events\Manager();

    $logger = new \Phalcon\Logger\Adapter\Firephp();

    $adapter = new \Phalcon\Db\Adapter\Pdo\Mysql(
        array(
            "host" => $config->database->host,
            "username" => $config->database->username,
            "password" => $config->database->password,
            "dbname" => $config->database->dbname,
            "charset" => 'utf8',
        )
    );
    $adapter->setEventsManager($eventsManager);
    return $adapter;
}, true);

//Create a console application
$console = new \Phalcon\CLI\Console();
$console->setDI($di);

/**
 * Process the console arguments
 */
$arguments = array();
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments[] = $arg;
    }
}

// define global constants for the current task and action
define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    // handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}
