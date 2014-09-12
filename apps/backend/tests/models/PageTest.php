<?php
namespace Robinson\Backend\Tests\Models;

class PageTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('pages');
    }

    public function testCreatingPageShouldPersistDataToDb()
    {
        /** @var \Robinson\Backend\Models\Page $page */
        $page = $this->getDI()->get('Robinson\Backend\Models\Page');
        $page->setTitle('test title');
        $page->setBody('test body');
        $page->create();
        $this->assertEquals('test title', $page->getTitle());
        $this->assertEquals('test body', $page->getBody());
        $this->assertEquals('test-title', $page->getSlug());
    }
}
