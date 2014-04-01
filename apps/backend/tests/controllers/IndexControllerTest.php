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
        $request = $this->getMock('Phalcon\Http\Request', array('isPost', 'getPost'));
        $request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        $request->expects($this->at(1))
            ->method('getPost')
            ->with($this->equalTo('username'))
            ->will($this->returnValue('test'));
        $request->expects($this->at(2))
            ->method('getPost')
            ->with($this->equalTo('password'))
            ->will($this->returnValue('testpassword'));
        
        $this->getDI()->setShared('request', $request);
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
        $this->assertRedirectTo('/admin/index/index');

    }
}