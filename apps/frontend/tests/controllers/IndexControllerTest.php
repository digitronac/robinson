<?php
namespace Robinson\Frontend\Tests\Controllers;

class IndexControllerTest extends BaseTestController
{
    protected $vfsStream;
    
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
        $imageMock = $this->getMockBuilder('Imagine\Imagick\ImageInterface')
            ->setMethods(array('thumbnail', 'save', 'getSize', 'paste', 'resize'))
            ->getMock();
        $imageMock->expects($this->any())
            ->method('thumbnail')
            ->will($this->returnSelf());
        $imageMock->expects($this->any())
            ->method('resize')
            ->will($this->returnValue(true));
        $imageMock->expects($this->any())
            ->method('save')
            ->will($this->returnValue(true));
        $imageMock->expects($this->any())
            ->method('paste')
            ->will($this->returnSelf());
        $imageMock->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(new \Imagine\Image\Box(600, 300)));

        $imagineMock->expects($this->any())
            ->method('open')
            ->will($this->returnValue($imageMock));
        $this->getDI()->set('imagine', $imagineMock);
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');

        $this->assertResponseContentContains('<img alt="package1" src="/img/package/250x1000/1-testpackageimage-1.jpg" />');
    }
} 