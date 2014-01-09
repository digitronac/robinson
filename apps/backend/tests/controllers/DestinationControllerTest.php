<?php
namespace Robinson\Backend\Tests\Controllers;
//@codingStandardsIgnoreStart
class DestinationControllerTest extends BaseTestController
{
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('Category');
     //   $this->populateTable('ImageCategory');
        $this->populateTable('Destinations');
    }
    
    public function testIndexActionShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/destination/index');
        $this->assertAction('index');
        $this->assertController('destination');
    }
}