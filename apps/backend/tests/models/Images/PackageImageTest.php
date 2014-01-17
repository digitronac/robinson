<?php
namespace Robinson\Backend\Tests\Models\Images;
// @codingStandardsIgnoreStart
class PackageImage extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('package_images');
    }
    
    public function testCanCreateModel()
    {
        $model = new \Robinson\Backend\Models\PackageImage();
        $this->assertInstanceOf('Robinson\Backend\Models\PackageImage', $model);
    }
    
    
}
