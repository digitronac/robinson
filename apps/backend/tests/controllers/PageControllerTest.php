<?php
namespace Robinson\Backend\Tests\Controllers;

class PageControllerTest extends \Robinson\Backend\Tests\Controllers\BaseTestController
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('pages');
    }

    public function testIndexPageActionShouldExist()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/page/index');
        $this->assertContains('<li><a href="/admin/page/update?pageId=3">title3</a></li>', $this->getContent());
    }

    public function testCreatePageActionShouldExist()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/page/create');
        $this->assertContains('<textarea id="body" name="body" class="ckeditor" required="required"></textarea>', $this->getContent());
    }

    public function testCreatingPageShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_POST = array(
            'title' => 'testing title 1',
            'body' => 'testing body 1',
        );
        $this->dispatch('/admin/page/create');
        $this->assertRedirectTo('/admin/page/update?pageId=4');
    }

    public function testUpdatePageActionShouldExist()
    {
        $this->registerMockSession();
        $_GET['pageId'] = 3;
        $this->dispatch('/admin/page/update');
        $this->assertContains('<textarea id="body" name="body" class="ckeditor" required="required">body3</textarea>', $this->getContent());
    }

    public function testUpdatingPageShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_GET['pageId'] = 3;
        $_POST = array(
            'title' => 'testing title 33',
            'body' => 'testing body 33',
        );
        $this->dispatch('/admin/page/update');
        $this->assertRedirectTo('/admin/page/update?pageId=3');
    }
}

