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
                'Robinson\Frontend\Filter'      => __DIR__ . '/../frontend/filters/',
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
        // @codeCoverageIgnoreStart
        if (!$di->has('config')) {
            $config = new \Zend_Config_Ini(MODULE_PATH . '/config/application.ini', APPLICATION_ENV);
            $config = new \Phalcon\Config($config->toArray());
            if (is_file(MODULE_PATH . '/config/application.local.ini')) {
                $local = (new \Zend_Config_Ini(MODULE_PATH . '/config/application.local.ini', APPLICATION_ENV));
                $local = new \Phalcon\Config($local->toArray());
                $config->merge($local);
            }
        } else {
            $config = $di->get('config');
        }
        // @codeCoverageIgnoreEnd


        include APPLICATION_PATH . '/backend/config/services.php';

        // listen for exceptions if debug ip
        if (in_array(
            $di['request']->getClientAddress(),
            $di['config']->application->debug->ips->toArray()
        )) {
            (new \Phalcon\Debug())->listen();
        }

        return $di;

    }
}
