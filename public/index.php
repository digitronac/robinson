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
}

register_shutdown_function(function() use ($di)
{
    $lastError = error_get_last();
    if(!isset($lastError['message']) || !$di->has('log'))
    {
        return;
    }
    $di['log']->error($lastError['message']);
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

$translator = new \Zend\Mvc\I18n\Translator();
$translator->addTranslationFile(
    'phparray',
    APPLICATION_PATH . '/../data/translations/zend_validate/sr.php',
    'default',
    'sr'
);
$translator->setLocale('sr');
\Zend\Validator\AbstractValidator::setDefaultTranslator($translator);


echo $application->handle()->getContent();


