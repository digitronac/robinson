<?php

namespace Robinson\Frontend;

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
			'Robinson\Frontend\Controllers' => __DIR__ . '/controllers/',
			'Robinson\Frontend\Models' => __DIR__ . '/models/',
		));

		$loader->register();
	}

	/**
	 * Registers the module-only services
	 *
	 * @param \Phalcon\DI $di
	 */
	public function registerServices($di)
	{

		/**
		 * Read configuration
		 */
		$config = include __DIR__ . "/config/config.php";
                
                $di->set('dispatcher', function() use ($di)
                {
                    $dispatcher = new \Phalcon\Mvc\Dispatcher();
                    $dispatcher->setDefaultNamespace("Frontend\Controllers\\");
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
				"dbname" => $config->database->name
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
                
	}

}