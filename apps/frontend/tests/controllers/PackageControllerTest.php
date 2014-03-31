<?php
namespace Robinson\Frontend\Tests\Controllers;

class PackageControllerTest extends BaseTestController
{
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('categories');
        $this->populateTable('destinations');
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

        $this->dispatch('/fixture-category/fixture-destination-1/package1/1');

        $this->assertController('package');
        $this->assertAction('index');
        $this->assertResponseContentContains('description1');
        $this->assertResponseContentContains('slider1_container');
    }

    public function testOkContactFormShouldSetProperFlashMessage()
    {
        $imagineMock = $this->getMockBuilder('Imagine\Imagick\ImagineInterface')
            ->setMethods(array('open'))
            ->getMock();
       $imageMock = $this->mockImage();

        $imagineMock->expects($this->any())
            ->method('open')
            ->will($this->returnValue($imageMock));
        $this->getDI()->set('imagine', $imagineMock);

        $_POST = array(
            'email' => 'test@example.org',
            'body' => 'email body',
        );

        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('isPost'))
            ->getMock();
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));

        $this->getDI()->setShared('request', $request);
        $this->dispatch('/fixture-category/fixture-destination-1/package1/1');
        $this->assertRedirectTo('/#contact-form');
        $this->assertEquals('Vaša poruka je poslata! Odgovorićemo u najkraćem mogućem roku! HVALA!!! :)',
            $this->getDI()->getShared('flashSession')->getMessages('success')[0]);
    }

    public function testInvalidContactFormShouldSetProperFlashMessage()
    {
        $imagineMock = $this->getMockBuilder('Imagine\Imagick\ImagineInterface')
            ->setMethods(array('open'))
            ->getMock();
        $imageMock = $this->getMockBuilder('Imagine\Imagick\ImageInterface')
            ->setMethods(array('thumbnail', 'save', 'getSize', 'paste', 'resize', 'usePalette'))
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
        $imageMock->expects($this->any())
            ->method('usePalette')
            ->will($this->returnValue(true));

        $imagineMock->expects($this->any())
            ->method('open')
            ->will($this->returnValue($imageMock));
        $this->getDI()->set('imagine', $imagineMock);

        $_POST = array(
            'email' => 'test @ example.org',
            'body' => '123',
        );

        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('isPost'))
            ->getMock();
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));

        $this->getDI()->setShared('request', $request);
        $this->dispatch('/fixture-category/fixture-destination-1/package1/1');
        $this->assertRedirectTo('/#contact-form');
        $this->assertCount(6, $this->getDI()->getShared('flashSession')->getMessages('email-error')[0]);
        $this->assertCount(1, $this->getDI()->getShared('flashSession')->getMessages('body-error')[0]);
    }

    public function testAccessingInvisiblePackageShouldRedirectToIndex()
    {
        $this->dispatch('/fixture-category/fixture-destination-3/package3/3');
        $this->assertRedirectTo('/');
    }


} 