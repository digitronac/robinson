<?php
// @codingStandardsIgnoreStart
namespace Robinson\Backend\Tests\Models\Tags;
class PackageTagTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('packages');
        $this->populateTable('package_tags');
    }
    
    public function testCanMakeModel()
    {
        $model = $this->getDI()->get('Robinson\Backend\Models\Tags\Package');
        $this->assertInstanceOf('Robinson\Backend\Models\Tags\Package', $model);
    }
    
    public function testCanAddTag()
    {
        /* @var $tag \Robinson\Backend\Models\Tags\Package */
        $tag = $this->getDI()->get('Robinson\Backend\Models\Tags\Package');
        
    }
}