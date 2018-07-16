<?php

/**
 * Services are globally registered in this file
 */

use Phalcon\Mvc\Router,
    Phalcon\Mvc\Url as UrlResolver,
    Phalcon\DI\FactoryDefault,
    Phalcon\Session\Adapter\Files as SessionAdapter;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Registering a router
 */

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di['url'] = function() {
    $url = new UrlResolver();
    $url->setBaseUri('/');
    return $url;
};

/**
 * Start the session the first time some component request the session service
 */
$di['session'] = function() {
    $session = new SessionAdapter();
    $session->start();
    return $session;
};

$di['response'] = function()
{
    $response = new \Phalcon\Http\Response();
    $response->setStatusCode(200, 'OK');
    return $response;
};

$di['debug'] = function()
{
    return new \Phalcon\Debug\Dump();
};

$di['fs'] = function()
{
    return new \Symfony\Component\Filesystem\Filesystem();
};

$di->set('imagine', function()
{
    return new \Imagine\Imagick\Imagine();
});


$di->set('modelsMetadata', function () use ($di) {
    return new \Phalcon\Mvc\Model\MetaData\Memcache(array(
        'host' => $di->getShared('config')->application->memcache->host,
        'lifetime' => 60,
    ));
}, true);

$di->set('memcache', function() use ($di) {
        //Cache data for one day by default
        $frontCache = new \Phalcon\Cache\Frontend\Data(array(
            "lifetime" => 60
        ));

        //Memcached connection settings
        $cache = new \Phalcon\Cache\Backend\Memcache($frontCache, array(
            "host" => $di->getShared('config')->application->memcache->host,
            "port" => "11211"
        ));

        return $cache;
}, true);

$di->set('modelsCache', function() use ($di) {
    //Cache data for one day by default
    $frontCache = new \Phalcon\Cache\Frontend\Data(array(
        "lifetime" => 60
    ));

    //Memcached connection settings
    $cache = new \Phalcon\Cache\Backend\Memcache($frontCache, array(
        "host" => $di->getShared('config')->application->memcache->host,
        "port" => "11211"
    ));

    return $cache;
}, true);


$di['watermark'] = function() use ($di)
{
    $filter = new \Robinson\Backend\Filter\Watermark(new \Imagick($di->getShared('config')
        ->application->watermark->watermark));
    return $filter;
};

$di['router'] = function() use ($di) {
        $router = new Router();
        $router->removeExtraSlashes(true);
        $router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);

        $router->setDefaultModule("frontend");
        $router->setDefaultNamespace("Robinson\Frontend\Controllers");

        if (strpos('http://insideserbia.com', $_SERVER['HTTP_HOST'])) {
            $router->add('/', array
            (
                'module' => 'frontend',
                'namespace' => 'Robinson\Frontend\Controllers\\',
                'controller' => 'index',
                'action' => 'english',
            ))
            ->setName('index');
        } else {
            $router->add('/', array
            (
                'module' => 'frontend',
                'namespace' => 'Robinson\Frontend\Controllers\\',
                'controller' => 'index',
                'action' => 'index',
            ))
            ->setName('index');
        }


        // add frontend

        $router->add('/([a-z0-9\-]+)/:int', array
        (
            'module' => 'frontend',
            'namespace' => 'Robinson\Frontend\Controllers\\',
            'controller' => 'category',
            'action' => 'index',
            'id' => 2,
        ))
        ->setName('category');

        $router->add('/pdf/:int', array
        (
            'module' => 'frontend',
            'namespace' => 'Robinson\Frontend\Controllers\\',
            'controller' => 'package',
            'action' => 'pdf',
            'id' => 1,
        ))
        ->setName('pdf');

        $router->add('/([a-z0-9\-]+)/([a-z0-9\-]+)/:int', array
        (
            'module' => 'frontend',
            'namespace' => 'Robinson\Frontend\Controllers\\',
            'controller' => 'destination',
            'action' => 'index',
            'id' => 3,
        ))
        ->setName('destination');

        $router->add('/([a-z0-9\-]+)/([a-z0-9\-]+)/([a-z0-9\-]+)/:int', array
        (
            'module' => 'frontend',
            'namespace' => 'Robinson\Frontend\Controllers\\',
            'controller' => 'package',
            'action' => 'index',
            'id' => 4,
        ))
        ->setName('package');

        // add backend
        $router->add('/admin', array
        (
            'module' => "backend",
            'namespace' => 'Robinson\Backend\Controllers\\',
            'controller' => 'index',
            'action' => "index",
        ))->setName('admin-index');

       $router->add('/admin/:controller/:action', array(
        'module' => 'backend',
        'namespace' => 'Robinson\Backend\Controllers\\',
        'controller' => 1,
        'action' => 2,
    ))->setName('admin');

       $router->add('/admin/:controller/:action/:int', array
       (
            'module' => 'backend',
            'namespace' => 'Robinson\Backend\Controllers\\',
            'controller' => 1,
            'action' => 2,
            'id' => 3,
       ))->setName('admin-update');

    return $router;
};


\Phalcon\Mvc\Model::setup(array(
    'virtualForeignKeys' => false,
    'columnRenaming' => false,
    'notNullValidations' => true,
    'exceptionOnFailedSave' => true,
));