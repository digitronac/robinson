<?php
namespace Robinson\Frontend\Tests\Models;
// @codingStandardsIgnoreStart
class BaseTestModel extends \Phalcon\Test\ModelTestCase
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        /**
        * Include services
        */
        require APPLICATION_PATH . '/../config/services.php';

        $config = new \Phalcon\Config(
            (new \Zend_Config_Ini(APPLICATION_PATH . '/frontend/config/application.ini', APPLICATION_ENV))->toArray()
        );
        if(is_file(APPLICATION_PATH . '/frontend/config/application.local.ini'))
        {
            $local = new \Phalcon\Config(
                (
                    new \Zend_Config_Ini(
                        APPLICATION_PATH . '/frontend/config/application.local.ini',
                        APPLICATION_ENV
                    )
                )->toArray()
            );
            $config->merge($local);
        }
                
        $di = include APPLICATION_PATH . '/frontend/config/services.php';
        
        parent::setUp($di, $config);
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

        $connection = $this->di->get('db');
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