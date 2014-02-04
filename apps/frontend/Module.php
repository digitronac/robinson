<?php

namespace Robinson\Frontend;

use Phalcon\Loader,
	Phalcon\Mvc\View,
	Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
	Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{

    /**
     * Registers the module auto-loader.
     *
     * @return void
     */
    public function registerAutoloaders($di)
    {
        if (!defined('MODULE_PATH'))
        {
            define('MODULE_PATH', __DIR__);
        }

        $loader = new Loader();

        $loader->registerNamespaces(
            array
            (
                'Robinson\Frontend\Controllers' => __DIR__ . '/controllers/',
                'Robinson\Frontend\Model'      => __DIR__ . '/models/',
                'Robinson\Frontend\Plugin'      => __DIR__ . '/plugins/',
                'Robinson\Frontend\Validator'   => __DIR__ . '/validators/',
                'Robinson\Frontend\Tag'         => __DIR__ . '/tags/',
                'Robinson\Frontend\Filter'      => __DIR__ . '/filters/',
            )
        );

        $loader->register();
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di di
     *
     * @return \Phalcon\DI
     */
    public function registerServices($di)
    {
        $config = new \Phalcon\Config\Adapter\Ini(MODULE_PATH . '/config/application.ini');
        if (is_file(MODULE_PATH . '/config/application.local.ini'))
        {
            $local = (new \Phalcon\Config\Adapter\Ini(__DIR__ . '/config/application.local.ini'));
            $config->merge($local);
        }
        $config = $config->get(APPLICATION_ENV);

        include APPLICATION_PATH . '/frontend/config/services.php';

        // listen for exceptions if debug ip
        if (in_array(
            $di->getService('request')->resolve()->getClientAddress(),
            $di->getService('config')->resolve()->application->debug->ips->toArray()
        )
        )
        {
            (new \Phalcon\Debug())->listen();
        }

        return $di;

    }

}