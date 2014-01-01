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

                include APPLICATION_PATH . '/backend/config/services.php';
                
                return $di;

	}

}