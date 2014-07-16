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
        $this->populateTable('pricelists');
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
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');

        $this->assertResponseContentContains('<img src="/img/package/250x1000/1-testpackageimage-1.jpg" alt="package1" />');
    }

    public function testContactActionShouldExist()
    {
        $this->dispatch('/index/contact');
        $this->assertController('index');
        $this->assertAction('contact');
    }

    public function testAgentsActionShouldExist()
    {
        $this->dispatch('/index/zaAgente');
        $this->assertController('index');
        $this->assertAction('zaAgente');
        $this->assertResponseContentContains('<li><a target="_blank" href="/pdf/pricelist/fixturepdf.pdf">fixturepdf.pdf</a></li>');
    }

    public function testWrongUrlWillShowNotFoundAction()
    {
        $this->dispatch('/somethingwhichdoesntexist');
        $this->assertController('index');
        $this->assertAction('notFound');
    }

    public function testSubmittingContactShouldWorkAsExpected()
    {
        $smtp = $this->getMockBuilder('Zend\Mail\Transport\Smtp')
            ->setMethods(array('send'))
            ->getMock();
        $smtp->expects($this->once())
            ->method('send')
            ->will($this->returnValue(true));
        $this->getDI()->set('Zend\Mail\Transport\Smtp', $smtp);

        $_POST = array(
            'name' => 'test',
            'email' => 'example@example.org',
            'phone' => '111111111111',
            'body' => 'msg body',
        );

        $this->getDI()->get('config')->application->smtp->port = 587;

        $this->dispatch('/index/contact');
        $this->assertController('index');
        $this->assertAction('contact');
        $this->assertResponseContentContains('<p class="successMessage">Vaša poruka je poslata! Odgovorićemo u najkraćem mogućem roku! HVALA!!! :)</p>');
    }

    public function testUsloviActionShouldExist()
    {
        $this->dispatch('/index/uslovi');
        $this->assertController('index');
        $this->assertAction('uslovi');
        $this->assertResponseCode(200);
    }

    public function testInformacijeActionShouldExist()
    {
        $this->dispatch('/index/informacije');
        $this->assertController('index');
        $this->assertAction('informacije');
        $this->assertResponseCode(200);
    }

    public function testPlacanjeActionShouldExist()
    {
        $this->dispatch('/index/placanje');
        $this->assertController('index');
        $this->assertAction('placanje');
        $this->assertResponseCode(200);
    }

    public function testOsiguranjeActionShouldExist()
    {
        $this->dispatch('/index/osiguranje');
        $this->assertController('index');
        $this->assertAction('osiguranje');
        $this->assertResponseCode(200);
    }

    public function testPrtljagActionShouldExist()
    {
        $this->dispatch('/index/prtljag');
        $this->assertController('index');
        $this->assertAction('prtljag');
        $this->assertResponseCode(200);
    }

    public function testDokumentaActionShouldExist()
    {
        $this->dispatch('/index/dokumenta');
        $this->assertController('index');
        $this->assertAction('dokumenta');
        $this->assertResponseCode(200);
    }

    /**
     * @expectedExceptionMessage Invalid email.
     * @expectedException \Exception
     */
    public function testSubmittingContactWithInvalidEmailShouldTriggerException()
    {
        $_POST = array(
            'name' => 'test',
            'email' => 'example',
            'phone' => '111111111111',
            'body' => 'msg body',
        );
        $this->dispatch('/index/contact');
        $this->assertController('index');
        $this->assertAction('contact');
    }
} 