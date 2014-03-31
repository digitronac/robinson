<?php
namespace Robinson\Frontend\Tests\Controllers;

class IndexControllerTest extends BaseTestController
{
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('packages');
        $this->populateTable('package_tabs');
        $this->populateTable('package_tags');
    }

    public function testIndexActionShouldWorkAsExpected()
    {
        $imagineMock = $this->getMockBuilder('Imagine\Imagick\ImagineInterface')
            ->setMethods(array('open'))
            ->getMock();
        $imageMock = $this->mockImage();

        $imagineMock->expects($this->any())
            ->method('open')
            ->will($this->returnValue($imageMock));
        $this->getDI()->set('imagine', $imagineMock);
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');

        $this->assertResponseContentContains('<img src="/img/package/250x1000/1-testpackageimage-1.jpg" alt="package1" />');
    }
} 