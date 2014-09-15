<?php
namespace Robinson\Frontend\Tests\Controllers;

class PageControllerTest extends BaseTestController
{
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('pages');
    }

    public function testLandingPageShouldContainPageLinks()
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

        $this->dispatch('/');
        $this->assertContains('<li><a href="/page/index?pageId=2">title2</a></li>', $this->getContent());
    }

    public function testPageIndexActionShouldDisplayExpectedData()
    {
        $_GET['pageId'] = 2;
        $this->dispatch('/page/index');
        $this->assertResponseContentContains('<h1>title2</h1>');
        $this->assertResponseContentContains('body2');
    }
}