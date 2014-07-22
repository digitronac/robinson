<?php
namespace Robinson\Backend\Tests\Controllers;
// @codingStandardsIgnoreStart
class IndexControllerTest extends BaseTestController
{
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('categories');
        $this->populateTable('category_images');
        $this->populateTable('destinations');
        $this->populateTable('packages');
        $this->populateTable('package_tags');
        $this->populateTable('pricelists');
    }
    
    public function testIndexActionShouldShowLogin()
    {
        $this->dispatch('/admin');
        $this->assertAction('index');
        $this->assertController('index');
        $this->assertResponseContentContains('<input class="form-control" type="password" name="password" placeholder="Password" required="required" />');
    }
    
    public function testUserOnIndexActionShouldBeRedirectedToDashboard()
    {
        $this->getDI()->getShared('session')->set('auth', 'User');
        $this->dispatch('/admin');
        $this->assertAction('index');
        $this->assertController('index');
        $this->assertRedirectTo('/admin/index/dashboard');
    }
    
    public function testAccessingPrivateActionAsGuestShouldForwardToIndexAction()
    {
        $this->dispatch('/admin/index/dashboard');
        $this->assertDispatchIsForwarded();
        $this->assertAction('index');
        $this->assertController('index');
    }
    
    public function testLoginShouldSetSession()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array(
            'username' => 'test',
            'password' => 'testpassword',
        );

        $stub = new \Robinson\Backend\Validator\Login(include __DIR__ . '/../fixtures/credentials.php');
        $this->getDI()->set('Robinson\Backend\Validator\Login', $stub);
        $this->dispatch('/admin/index/index');
        $this->assertRedirectTo('/admin/index/dashboard');
    }

    public function testDashboardExists()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/index/dashboard');
        $this->assertResponseContentContains('<li><a href="/admin/destination/update/5">fixture destination 5</a></li>');
        $this->assertAction('dashboard');
    }

    public function testLogoutActionShouldNotThrowException()
    {
        $this->registerMockSession();

        $sessionMock = $this->getMockBuilder('Phalcon\Session\Adapter\Files')
            ->setMethods(array('destroy', 'auth'))
            ->disableOriginalConstructor()
            ->getMock();
        $sessionMock->expects($this->any())
            ->method('get')
            ->with($this->equalTo('auth'))
            ->will($this->returnValue(array('username' => 'nemanja')));
        $this->getDI()->set('session', $sessionMock);
        $this->dispatch('/admin/index/logout');
    }

    public function testSortTaggedPackagesAction()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/index/sortTaggedPackages');
        $this->assertResponseContentContains('package1 - <input type="text" name="packageTagIds[1]" value="1" size="2"');
    }

    public function testSortTaggedPackagesActionByLastMinute()
    {
        $this->registerMockSession();
        $_GET['type'] = 2;
        $this->dispatch('/admin/index/sortTaggedPackages');
        $this->assertResponseContentContains('package2 - <input type="text" name="packageTagIds[2]" value="2" size="2" />');
    }

    public function testSortTaggedPackagesActionReordering()
    {
        $this->registerMockSession();
        $_POST['packageTagIds'] = array(
            1 => 2,
            2 => 5,
        );

        $this->dispatch('/admin/index/sortTaggedPackages');
        foreach ($_POST['packageTagIds'] as $packageTagId => $order) {
            $tag = \Robinson\Backend\Models\Tags\Package::findFirst($packageTagId);
            $this->assertEquals($order, $tag->getOrder());
        }
    }

    public function testUploadingPricelistShouldSaveRecordToDatabase()
    {
        $this->registerMockSession();

        $mockFile = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->disableOriginalConstructor()
            ->setMethods(array('moveTo', 'getName'))
            ->getMock();
        $mockFile->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(true));
        $mockFile->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('pdf.pdf'));
        $mockRequest = $this->getMockBuilder('Phalcon\Http\Request\Request')
            ->disableOriginalConstructor()
            ->setMethods(array('getUploadedFiles', 'getClientAddress', 'hasFiles', 'getQuery'))
            ->getMock();
        $mockRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array($mockFile)));
        $mockRequest->expects($this->once())
            ->method('hasFiles')
            ->will($this->returnValue(true));

        $this->getDI()->set('request', $mockRequest);
        $this->dispatch('/admin/index/agents');
        /** @var \Robinson\Backend\Models\Pricelist $pricelist */
        $pricelist = \Robinson\Backend\Models\Pricelist::findFirst(2);
        $this->assertEquals('pdf.pdf', $pricelist->getFilename());
        $this->assertEquals(
            $this->getDI()->get('config')->application->pricelistPdfWebPath . '/' . rawurlencode('pdf.pdf'),
            $pricelist->getLink()
        );
        $this->assertResponseContentContains('<a href="/pdf/pricelist/pdf.pdf">pdf.pdf</a> -
                    <a class="del" style="color:red" href="?pricelistId=2">Obriši</a>');
    }

    public function testDeletingPriceListShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $mockFilesystem = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->setMethods(array('remove'))
            ->getMock();
        $this->getDI()->set('Symfony\Component\Filesystem\Filesystem', $mockFilesystem);
        $_GET['pricelistId'] = 1;
        $this->dispatch('/admin/index/agents');
        $this->assertFalse(\Robinson\Backend\Models\Pricelist::findFirst($_GET['pricelistId']));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Pricelist fixturepdf.pdf already exists.
     */
    public function testUploadingPricelistWithExistingFilenameShouldThrowException()
    {
        $this->registerMockSession();

        $mockFile = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->disableOriginalConstructor()
            ->setMethods(array('moveTo', 'getName'))
            ->getMock();
        $mockFile->expects($this->never())
            ->method('moveTo')
            ->will($this->returnValue(true));
        $mockFile->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('fixturepdf.pdf'));
        $mockRequest = $this->getMockBuilder('Phalcon\Http\Request\Request')
            ->disableOriginalConstructor()
            ->setMethods(array('getUploadedFiles', 'getClientAddress', 'hasFiles', 'getQuery'))
            ->getMock();
        $mockRequest->expects($this->once())
            ->method('getClientAddress')
            ->will($this->returnValue(true));
        $mockRequest->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array($mockFile)));
        $mockRequest->expects($this->once())
            ->method('hasFiles')
            ->will($this->returnValue(true));
        $this->getDI()->set('request', $mockRequest);

        $mockFilesystem = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->setMethods(array('exists'))
            ->getMock();
        $mockFilesystem->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(true));
        $this->getDI()->set('Symfony\Component\Filesystem\Filesystem', $mockFilesystem);

        $this->dispatch('/admin/index/agents');
    }
}