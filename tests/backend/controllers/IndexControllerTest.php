<?php
namespace Robinson\Backend\Controllers;
class IndexControllerTest extends \Phalcon\Test\FunctionalTestCase
{
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        /**
        * Include services
        */
        require APPLICATION_PATH . '/../config/services.php';

        $config = include APPLICATION_PATH . '/backend/config/config.php';
        $di = include APPLICATION_PATH . '/backend/config/services.php';
        parent::setUp($di, $config);
        
        $this->application->registerModules(array
        (
            'backend' => array
            (
                'className' => 'Robinson\Backend\Module',
                'path' => APPLICATION_PATH . '/backend/Module.php',
            ),
        ));

    }
    
    public function testIndexActionShouldShowLogin()
    {
        $this->dispatch('/admin');
        $this->assertAction('index');
        $this->assertController('index');
        $this->assertResponseContentContains('<input type="password" name="password" placeholder="Password" required="required" />');
    }
}