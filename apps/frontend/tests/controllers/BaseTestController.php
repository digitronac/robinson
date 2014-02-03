<?php
namespace Robinson\Frontend\Tests\Controllers;
// @codingStandardsIgnoreStart
class BaseTestController extends \Phalcon\Test\FunctionalTestCase
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        /**
        * Include services
        */
        require APPLICATION_PATH . '/../config/services.php';

        $config = new \Phalcon\Config\Adapter\Ini(APPLICATION_PATH . '/frontend/config/application.ini');
        if(is_file(APPLICATION_PATH . '/frontend/config/application.local.ini'))
        {
            $local = (new \Phalcon\Config\Adapter\Ini(APPLICATION_PATH . '/frontend/config/application.local.ini'));
            $config->merge($local);
        }
        $config = $config->get(APPLICATION_ENV);
                
        $di = include APPLICATION_PATH . '/frontend/config/services.php';
        
        parent::setUp($di, $config);
        
        $this->application->registerModules(array
        (
            'backend' => array
            (
                'className' => 'Robinson\Frontend\Module',
                'path' => APPLICATION_PATH . '/frontend/Module.php',
            ),
        ));
    }
    
    protected function registerMockSession()
    {
        $sessionMock = $this->getMock('Phalcon\Session\Adapter\Files', array('get'));
        $sessionMock->expects($this->any())
            ->method('get')
            ->with($this->equalTo('auth'))
            ->will($this->returnValue(array('username' => 'nemanja')));
        $this->getDI()->set('session', $sessionMock);
    }
    
    /**
    * Populates a table with default data
    *
    * @param      $table
    * @param null $records
    * @author Nikos Dimopoulos <nikos@phalconphp.com>
    * @since  2012-11-08
    */
   public function populateTable($table, $records = null)
   {
        // Empty the table first
        $this->truncateTable($table);

        $connection = $this->di->getShared('db');
        $parts = explode('_', $table);
        $suffix = '';

        foreach ($parts as $part) {
                $suffix .= ucfirst($part);
        }

        $class = 'Phalcon\Test\Fixtures\\' . $suffix;

        $data = $class::get($records);

        foreach ($data as $record) {
                $sql = "INSERT INTO {$table} VALUES " . $record;
                $connection->execute($sql);
        }
   }
   
   protected function mockWorkingImagick()
   {
       $mockImagick = $this->getMockBuilder('Imagick')
           ->setMethods(array('scaleimage', 'writeimage', 'getimageheight', 'getimagewidth', 'compositeimage',))
           ->disableOriginalConstructor()
           ->getMock();
       
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('getimagewidth')
            ->will($this->returnValue(640));
        $mockImagick->expects($this->any())
            ->method('getimageheight')
            ->will($this->returnValue(480));
        $mockImagick->expects($this->any())
            ->method('compositeimage')
            ->will($this->returnValue(true));
        return $mockImagick;
   }
       
       
}