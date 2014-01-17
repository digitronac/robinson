<?php
namespace Robinson\Tests\Models;
// @codingStandardsIgnoreStart
class PackageTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('packages');
    }
    
    public function testCanCreateModel()
    {
        $model = $this->getDI()->get('Robinson\Backend\Models\Package');
        $this->assertInstanceOf('Robinson\Backend\Models\Package', $model);
    }
}