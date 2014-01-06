<?php
namespace Robinson\Backend\Tests\Models;
class CategoryTest extends \Phalcon\Test\UnitTestCase
{
    public function testGetUpdateUrlShouldReturnPath()
    {
        $router = new \Phalcon\Mvc\Router();
        $router->setDefaultModule("frontend");
	$router->setDefaultNamespace("Robinson\Frontend\Controllers");
        $router->add('/admin/:controller/:action/:int', array
        (
            'module' => 'backend',
            'namespace' => 'Robinson\Backend\Controllers\\',
            'controller' => 1,
            'action' => 2,
            'id' => 3,
        ))->setName('admin-update');
        $url = new \Phalcon\Mvc\Url();
        $di = new \Phalcon\DI();
        $di->set('url', $url);
        $di->set('router', $router);
        $categoryMock = $this->getDI()->get('Robinson\Backend\Models\Category');
        $categoryMock->setCategoryId(3);
        $categoryMock->setDI($di);
        $this->assertContains('/admin/category/update/3', $categoryMock->getUpdateUrl());
    }
}