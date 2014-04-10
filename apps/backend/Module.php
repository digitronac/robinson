<?php
namespace Robinson\Backend;

class Module implements \Phalcon\Mvc\ModuleDefinitionInterface
{

    /**
     * Registers the module auto-loader.
     *
     * @return void
     */
    public function registerAutoloaders()
    {
        if (!defined('MODULE_PATH')) {
            define('MODULE_PATH', __DIR__);
        }

        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces(
            array
            (
                'Robinson\Backend\Controllers' => __DIR__ . '/controllers/',
                'Robinson\Backend\Models'      => __DIR__ . '/models/',
                'Robinson\Backend\Plugin'      => __DIR__ . '/plugins/',
                'Robinson\Backend\Validator'   => __DIR__ . '/validators/',
                'Robinson\Backend\Tag'         => __DIR__ . '/tags/',
                'Robinson\Backend\Filter'      => __DIR__ . '/filters/',
            )
        );

        $loader->register();
    }

    /**
     * Registers the module-only services
     *
     * @param \Phalcon\DI $di di
     *
     * @return \Phalcon\DI
     */
    public function registerServices($di)
    {
        $config = new \Phalcon\Config\Adapter\Ini(MODULE_PATH . '/config/application.ini');
        if (is_file(MODULE_PATH . '/config/application.local.ini')) {
            $local = (new \Phalcon\Config\Adapter\Ini(__DIR__ . '/config/application.local.ini'));
            $config->merge($local);
        }
        $config = $config->get(APPLICATION_ENV);

        include APPLICATION_PATH . '/backend/config/services.php';

        // listen for exceptions if debug ip
        if (in_array(
            $di->getService('request')->resolve()->getClientAddress(),
            $di->getService('config')->resolve()->application->debug->ips->toArray()
        )) {
            (new \Phalcon\Debug())->listen();
        }

        return $di;

    }
}
