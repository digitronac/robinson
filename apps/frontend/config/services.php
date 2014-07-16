<?php

$di->setShared('config', function () use ($config) {
    return $config;
});

$di->set('dispatcher', function () use ($di) {
    /* @var $eventsManger \Phalcon\Events\Manager */
    $eventsManager = $di->getShared('eventsManager');

    $eventsManager->attach(
        'dispatch:beforeException',
        function ($event, $dispatcher, $exception) {
            switch ($exception->getCode()) {
                case \Phalcon\Mvc\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                case \Phalcon\Mvc\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    $dispatcher->forward(
                        array(
                            'controller' => 'index',
                            'action' => 'notFound',
                        )
                    );
                    return false;
            }
        }
    );

    $dispatcher = new \Phalcon\Mvc\Dispatcher();
    $dispatcher->setDefaultNamespace("Robinson\Frontend\Controllers\\");
    $dispatcher->setEventsManager($eventsManager);
    // For frontend's module.php: $dispatcher->setDefaultNamespace("Frontend\Controllers\\");
    return $dispatcher;
});

/**
 * Setting up the view component
 */
$di['view'] = function () {
    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir(APPLICATION_PATH . '/frontend/views/');
    return $view;
};

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di['db'] = function () use ($di) {
    $config = $di->getShared('config');
    $eventsManager = new \Phalcon\Events\Manager();

    $logger = new \Phalcon\Logger\Adapter\Firephp();
    //Listen all the database events
    /* $eventsManager->attach('db', function($event, $connection) use ($logger)
      {
      if ($event->getType() == 'beforeQuery')
      {
      $logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
      }
      }); */

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
};

// This function will 'divide' parts of the application with the correct url:
$di->set('url', function () use ($di) {
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri("/");
    // For frontend module.php:  $url->setBaseUri("/");
    return $url;
});

//Set the views cache service
$di->set('viewCache', function () {
    $frontCache = new \Phalcon\Cache\Frontend\Output(array(
        'lifetime' => 300,
    ));

    $cache = new \Phalcon\Cache\Backend\Memcache($frontCache, array(
        "host" => "localhost",
        "port" => "11211"
    ));
    return $cache;
});

// @codeCoverageIgnoreStart
$di->setShared('log', function () use ($di) {
    $log = new \Phalcon\Logger\Multiple();
    $logDir = APPLICATION_PATH . '/frontend/logs/' . date('Y') . '/' . date('m') . '/' . date('d');
    $logFile = $logDir . '/' . 'log.txt';
    
    if ($di->getService('config')->resolve()->application->log->enable) {
        if (!is_file($logFile)) {
            mkdir($logDir, 0775, true);

            if (!is_file($logFile)) {
                touch($logFile);
            }
        }

        $fileLogger = new \Phalcon\Logger\Adapter\File($logFile);
        $lineFormatter = new \Phalcon\Logger\Formatter\Line();
        $fileLogger->setFormatter($lineFormatter);
        $log->push($fileLogger);

        $fileLogger = new \Phalcon\Logger\Adapter\File($logFile);
        $firephpFormatter = new \Phalcon\Logger\Formatter\Firephp();
        $fileLogger->setFormatter($firephpFormatter);
        $log->push($fileLogger);
    }

    if (
        in_array(
            $di->getService('request')->resolve()->getClientAddress(),
            $di->getService('config')->resolve()->application->debug->ips->toArray()
        )
    ) {
        $fireLogger = new \Phalcon\Logger\Adapter\Firephp();
        $fireFormatter = new \Phalcon\Logger\Formatter\Firephp();
        $fireLogger->setFormatter($fireFormatter);
        $log->push($fireLogger);
    }

    return $log;
});

$di['watermark'] = function () use ($di) {
    $filter = new \Robinson\Backend\Filter\Watermark(
        new \Imagick($di->getShared('config')->application->watermark->watermark)
    );
    return $filter;
};

$di['translate'] = function () use ($di) {
    $translate = new \Phalcon\Translate\Adapter\NativeArray(
        array(
            'content' => include APPLICATION_PATH . '/../data/translations/sr.php',
        )
    );
    return $translate;
};
// @codeCoverageIgnoreEnd
return $di;
