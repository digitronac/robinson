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
                if(!defined('MODULE_PATH'))
                {
                    define('MODULE_PATH', __DIR__);
                }
                
		$loader = new Loader();

		$loader->registerNamespaces(array(
			'Robinson\Backend\Controllers' => __DIR__ . '/controllers/',
			'Robinson\Backend\Models' => __DIR__ . '/models/',
                        'Robinson\Backend\Plugin' => __DIR__ . '/plugins/',
                        'Robinson\Backend\Validator' => __DIR__ . '/validators/',
                        'Robinson\Backend\Tag' => __DIR__ . '/tags/',
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
                $config = new \Phalcon\Config\Adapter\Ini(MODULE_PATH . '/config/application.ini');
                if(is_file(MODULE_PATH . '/config/application.local.ini'))
                {
                    $local = (new \Phalcon\Config\Adapter\Ini(__DIR__ . '/config/application.local.ini'));
                    $config->merge($local);
                }
                $config = $config->get(APPLICATION_ENV);
              
                include APPLICATION_PATH . '/backend/config/services.php';
                
                return $di;

	}
}