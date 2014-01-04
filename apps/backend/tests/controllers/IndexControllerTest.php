<?php
namespace Robinson\Backend\Tests\Controllers;
class IndexControllerTest extends BaseTestController
{
    public function testIndexActionShouldShowLogin()
    {
        $this->dispatch('/admin');
        $this->assertAction('index');
        $this->assertController('index');
        $this->assertResponseContentContains('<input type="password" name="password" placeholder="Password" required="required" />');
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
}