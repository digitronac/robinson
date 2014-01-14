<?php
namespace Robinson\Backend\Tests\Models\Images;
// @codingStandardsIgnoreStart
class CategoryTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('Category');
        $this->populateTable('ImageCategory');
    }
    public function testCallingImageIdShouldWorkAsExpected()
    {
        $model = \Robinson\Backend\Models\Images\Category::findFirst();
        $this->assertEquals(1, $model->getImageId());
    }
    
    public function testMakingObjectWithoutConfigShouldWork()
    {
        $model = new \Robinson\Backend\Models\Images\Category();
        $this->getDI()->remove('config');
        $this->assertNull($model->getImagesPath());
    }
    
    public function testSettingImageCategoryIdShouldWorkAsExpected()
    {
        $model = new \Robinson\Backend\Models\Images\Category();
        $model->setCategoryId(1);
        $this->assertEquals(1, $model->getBelongsToId());
    }
}