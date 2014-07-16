<?php
namespace Robinson\Frontend\Tests\Controllers;

include_once APPLICATION_PATH . '/frontend/models/Pdf.php';

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

        $_SERVER['REQUEST_METHOD'] = 'POST';

        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('isPost'))
            ->getMock();
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));

        $this->getDI()->set('request', $request);

        $mockSmtpTransport = $this->getMockBuilder('Zend\Mail\Transport\Smtp')
            ->setMethods(array('send'))
            ->getMock();
        $mockSmtpTransport->expects($this->once())
            ->method('send')
            ->will($this->returnValue(true));
        $this->getDI()->set('Zend\Mail\Transport\Smtp', $mockSmtpTransport);

        $this->getDI()->get('config')->application->smtp->port = 587;

        $this->dispatch('/fixture-category/fixture-destination-1/package1/1');
        $this->assertRedirectTo('/#contact-form');
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
        //$this->assertRedirectTo('/#contact-form');
        $this->assertCount(6, $this->getDI()->getShared('flashSession')->getMessages('email-error')[0]);
        $this->assertCount(1, $this->getDI()->getShared('flashSession')->getMessages('body-error')[0]);
    }

    public function testPreviewingPdfWithExistentHtmlFileShouldDisplayOutput()
    {
        \org\bovigo\vfs\vfsStream::setup(
            'root',
            0775,
            array(
                'public' => array(
                    'pdf' => array(
                        'package' => array(
                            '1' => array(
                                'pdffile-1.pdf.html' =>
                                    '<html><head><base /><title>pdffile-1.pdf.html - test</title></head><body></body></html>',
                            ),
                        ),
                    ),
                )
            )
        );
        $this->getDI()['config']->application->packagePdfPath = 'vfs://root/public/pdf/package';
        $this->dispatch('/pdf/1');
        $this->assertResponseContentContains(
            '<html><head><base><base href="/pdf/package/1/"></head><body></body></html>'
        );
    }

    public function testPreviewingPdfWithNonExistentHtmlFileShouldGenerateHtmlFileAndDisplayOutput()
    {
        \org\bovigo\vfs\vfsStream::setup(
            'root',
            0775,
            array(
                'public' => array(
                    'pdf' => array(
                        'package' => array(
                            '1' => array(
                                'pdffile-1.pdf.html' =>
                                    '<html><head><base /><title>pdffile-1.pdf.html - test</title></head><body></body></html>',
                            ),
                        ),
                    ),
                )
            )
        );
        $this->getDI()['config']->application->packagePdfPath = 'vfs://root/public/pdf/package';
        $mockFilesystem = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->setMethods(array('exists'))
            ->getMock();
        $mockFilesystem->expects($this->at(0))
            ->method('exists')
            ->will($this->returnValue(false));
        $mockFilesystem->expects($this->at(1))
            ->method('exists')
            ->will($this->returnValue(true));
        $mockFilesystem->expects($this->at(2))
            ->method('exists')
            ->will($this->returnValue(true));

        $this->getDI()['fs'] = $mockFilesystem;

        $this->dispatch('/pdf/1');
        $this->assertResponseContentContains(
            '<html><head><base><base href="/pdf/package/1/"></head><body></body></html>'
        );
    }

    public function testAccessingInvisiblePackageShouldRedirectToIndex()
    {
        $this->dispatch('/fixture-category/fixture-destination-3/package3/3');
        $this->assertRedirectTo('/');
    }
}
