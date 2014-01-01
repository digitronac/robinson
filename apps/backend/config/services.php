<?php

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
$di['db'] = function() use ($config)
{
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
    "dbname" => $config->database->dbname
    ));
    $adapter->setEventsManager($eventsManager);
    return $adapter;
};

$di->setShared('config', function() use ($config)
{
    return $config;
});

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
    'index' => array('test', 'dashboard'),
    'category' => array('create', 'update', 'delete', 'deleteImage'),
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

return $di;
