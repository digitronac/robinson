<?php
namespace Robinson\Frontend;

class Module implements \Phalcon\Mvc\ModuleDefinitionInterface
{

    /**
     * Registers the module auto-loader.
     *
     * @return void
     */
    public function registerAutoloaders($di)
    {
        if (!defined('MODULE_PATH')) {
            define('MODULE_PATH', __DIR__);
        }

        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces(
            array
            (
                'Robinson\Frontend\Controllers' => __DIR__ . '/controllers/',
                'Robinson\Frontend\Model'      => __DIR__ . '/models/',
                'Robinson\Frontend\Plugin'      => __DIR__ . '/plugins/',
                'Robinson\Frontend\Validator'   => __DIR__ . '/validators/',
                'Robinson\Frontend\Tag'         => __DIR__ . '/tags/',
                'Robinson\Frontend\Filter'      => __DIR__ . '/filters/',
                'Robinson\Frontend\Form' => __DIR__ . '/forms/',
                'Robinson\Backend\Filter' => __DIR__ . '/../backend/filters/',
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
        $config = new \Phalcon\Config(
            (new \Zend_Config_Ini(MODULE_PATH . '/config/application.ini', APPLICATION_ENV))->toArray()
        );
        if (is_file(MODULE_PATH . '/config/application.local.ini')) {
            $local = new \Phalcon\Config(
                (new \Zend_Config_Ini(MODULE_PATH . '/config/application.local.ini', APPLICATION_ENV))->toArray()
            );
            $config->merge($local);
        }

        include APPLICATION_PATH . '/frontend/config/services.php';

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
