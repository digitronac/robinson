<?php

$di->setShared('config', function() use ($config)
{
    return $config;
});

$di->set('dispatcher', function() use ($di)
{
    $access = new \Robinson\Backend\Plugin\Access($di);
    /* @var $eventsManger \Phalcon\Events\Manager */
    $eventsManager = $di->getShared('eventsManager');
    $eventsManager->attach('dispatch', $access);

    $dispatcher = new \Phalcon\Mvc\Dispatcher();
    $dispatcher->setDefaultNamespace("Robinson\Backend\Controllers\\");
    $dispatcher->setEventsManager($eventsManager);
    // For frontend's module.php: $dispatcher->setDefaultNamespace("Frontend\Controllers\\");
    return $dispatcher;
});

/**
 * Setting up the view component
 */
$di['view'] = function()
{
    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir(APPLICATION_PATH . '/backend/views/');
    return $view;
};

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di['db'] = function() use ($di)
{
    $config = $di->getShared('config');
    $eventsManager = new \Phalcon\Events\Manager();

    $logger = new \Phalcon\Logger\Adapter\Firephp(); //("app/logs/debug.log");
    //Listen all the database events
    /* $eventsManager->attach('db', function($event, $connection) use ($logger) 
      {
      if ($event->getType() == 'beforeQuery')
      {
      $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
      }
      }); */

    $adapter = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
    "host" => $config->database->host,
    "username" => $config->database->username,
    "password" => $config->database->password,
    "dbname" => $config->database->dbname,
    "charset" => 'utf8',
    ));
    $adapter->setEventsManager($eventsManager);
    return $adapter;
};

// This function will 'divide' parts of the application with the correct url:
$di->set('url', function() use ($di)
{
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri("/");
    // For frontend module.php:  $url->setBaseUri("/");
    return $url;
});

$di->setShared('acl', function() use ($di)
{
    $acl = new \Phalcon\Acl\Adapter\Memory();
    $acl->setDefaultAction(\Phalcon\Acl::DENY);
    $roles = array
    (
        'user' => new \Phalcon\Acl\Role('User'),
        'guest' => new \Phalcon\Acl\Role('Guest'),
    );

    foreach ($roles as $role)
    {
        $acl->addRole($role);
    }

    $privateResources = array
    (
        'index' => array('dashboard', 'logout'),
        'category' => array('index', 'create', 'update', 'delete', 'deleteImage'),
        'destination' => array('index', 'create', 'update', 'delete', 'deleteImage'),
        'package' => array('index', 'create', 'update', 'delete', 'deleteImage', 'pdfPreview'),
    );

    $publicResources = array
    (
        'index' => array('index'),
    );

    foreach ($publicResources as $resource => $actions)
    {
        $acl->addResource(new \Phalcon\Acl\Resource($resource), $actions);
    }

    foreach ($roles as $role)
    {
        foreach ($publicResources as $resource => $actions)
        {
            $acl->allow($role->getName(), $resource, $actions);
        }
    }

    foreach ($privateResources as $resource => $actions)
    {
        $acl->addResource(new \Phalcon\Acl\Resource($resource), $actions);
    }

    foreach ($roles as $role)
    {
        foreach ($privateResources as $resource => $actions)
        {
            $acl->allow('User', $resource, $actions);
        }
    }

    return $acl;
});

$di['log'] = function() use ($di)
{
    $log = new \Phalcon\Logger\Multiple();
    $logDir = APPLICATION_PATH . '/backend/logs/' . date('Y') . '/' . date('m') . '/' . date('d');
    $logFile = $logDir . '/' . 'log.txt';
    
    if ($di->getService('config')->resolve()->application->log->enable)
    {
        if (!is_file($logFile))
        {
            umask(0);
            mkdir($logDir, 0775, true);

            if (!is_file($logFile))
            {
                touch($logFile);
            }
        }
        
        $fileLogger = new \Phalcon\Logger\Adapter\File($logFile);
        //$jsonFormatter = new \Phalcon\Logger\Formatter\Json();
        $fireFormatter = new \Phalcon\Logger\Formatter\Firephp();
        $fileLogger->setFormatter($fireFormatter);
        $log->push($fileLogger);  
    }

    if (in_array($di->getService('request')->resolve()->getClientAddress(), 
        $di->getService('config')->resolve()->application->debug->ips->toArray()))
    {
        $fireLogger = new \Phalcon\Logger\Adapter\Firephp();
        $fireFormatter = new \Phalcon\Logger\Formatter\Firephp();
        $fireLogger->setFormatter($fireFormatter);
        $log->push($fireLogger);
    }
    return $log;
};

$di['watermark'] = function() use ($di)
{
    $filter = new \Robinson\Backend\Filter\Watermark(new \Imagick($di->getShared('config')
        ->application->watermark->watermark));
    return $filter;
};

$di['assets'] = function() use ($di)
{
    $assets = new \Phalcon\Assets\Manager();
   // $assets->addCss('css/css.css');
    return $assets;
};

return $di;
