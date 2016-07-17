<?php
namespace Robinson\Frontend\Tests\Controllers;

class CategoryControllerTest extends BaseTestController
{
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('categories');
        $this->populateTable('packages');
        $this->populateTable('package_tabs');
        $this->populateTable('package_tags');
    }

    public function testIndexActionShouldWorkAsExpected()
    {
        $imagickMock = $this->mockWorkingImagick();
        $imagineMock = $this->getMockBuilder('Imagine\Imagick\ImagineInterface')
            ->setMethods(array('open','getImagick', 'thumbnail'))
            ->getMock();
        $imageMock = $this->mockImage();

        $imagineMock->expects($this->any())
            ->method('open')
            ->will($this->returnValue($imageMock));
        $imagineMock->expects($this->any())
            ->method('thumbnail')
            ->will($this->returnValue($imageMock));
        $imagineMock->expects($this->any())
            ->method('getImagick')
            ->will($this->returnValue($imagickMock));
        $this->getDI()->set('imagine', $imagineMock);

        $this->dispatch('/fixture-category/1');

        $this->assertController('category');
        $this->assertAction('index');
        $this->assertResponseContentContains('description test fixture category');
    }

    public function testUriWithLastYearShouldRedirectToThisYearUri()
    {
        $imagickMock = $this->mockWorkingImagick();
        $imagineMock = $this->getMockBuilder('Imagine\Imagick\ImagineInterface')
            ->setMethods(array('open','getImagick', 'thumbnail'))
            ->getMock();
        $imageMock = $this->mockImage();

        $imagineMock->expects($this->any())
            ->method('open')
            ->will($this->returnValue($imageMock));
        $imagineMock->expects($this->any())
            ->method('thumbnail')
            ->will($this->returnValue($imageMock));
        $imagineMock->expects($this->any())
            ->method('getImagick')
            ->will($this->returnValue($imagickMock));
        $this->getDI()->set('imagine', $imagineMock);

        $_SERVER['REQUEST_URI'] = '/2015-fixture-category/1';
        $this->dispatch('/2015-fixture-category/1');
        $this->assertResponseCode(301);
        $this->assertRedirectTo('/2016-fixture-category/1');
        $_SERVER['REQUEST_URI'] = '/';
    }
} 