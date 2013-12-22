<?php

namespace Robinson\Backend;

use Phalcon\Loader,
	Phalcon\Mvc\View,
	Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
	Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{

	/**
	 * Registers the module auto-loader
	 */
	public function registerAutoloaders()
	{

		$loader = new Loader();

		$loader->registerNamespaces(array(
			'Robinson\Backend\Controllers' => __DIR__ . '/controllers/',
			'Robinson\Backend\Models' => __DIR__ . '/models/',
                        'Robinson\Backend\Plugin' => __DIR__ . '/plugins/',
                        'Robinson\Backend\Validator' => __DIR__ . '/validators/',
		));

		$loader->register();
	}

	/**
	 * Registers the module-only services
	 *
	 * @param Phalcon\DI $di
	 */
	public function registerServices($di)
	{
		/**
		 * Read configuration
		 */
		$config = include __DIR__ . "/config/config.php";

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
		$di['view'] = function() {
			$view = new View();
			$view->setViewsDir(__DIR__ . '/views/');
			return $view;
		};

		/**
		 * Database connection is created based in the parameters defined in the configuration file
		 */
		$di['db'] = function() use ($config) {
			return new DbAdapter(array(
				"host" => $config->database->host,
				"username" => $config->database->username,
				"password" => $config->database->password,
				"dbname" => $config->database->dbname
			));
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

                    foreach($roles as $role)
                    {
                        $acl->addRole($role);
                    }
                    
                    $privateResources = array
                    (
                        'index' => array('test', 'dashboard'),
                        'category' => array('create', 'update', 'delete'),
                    );
                    
                    $publicResources = array
                    (
                        'index' => array('index'),
                    );
                    
                    foreach($publicResources as $resource => $actions)
                    {
                        $acl->addResource(new \Phalcon\Acl\Resource($resource), $actions);
                    }
                    
                    foreach($roles as $role)
                    {
                        foreach($publicResources as $resource => $actions)
                        {
                            $acl->allow($role->getName(), $resource, $actions);
                        }
                    }
                    
                    foreach($privateResources as $resource => $actions)
                    {
                        $acl->addResource(new \Phalcon\Acl\Resource($resource), $actions);
                    }
                    
                    foreach($roles as $role)
                    {
                        foreach($privateResources as $resource => $actions)
                        {
                            $acl->allow('User', $resource, $actions);
                        }
                    }
                    
                    return $acl;
                });

	}

}