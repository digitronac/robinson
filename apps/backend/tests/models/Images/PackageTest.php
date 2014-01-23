<?php
namespace Robinson\Backend\Tests\Models\Images;
// @codingStandardsIgnoreStart
class PackageTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('packages');
        $this->populateTable('package_images');
    }
    
    public function testCanCreateModel()
    {
        $model = new \Robinson\Backend\Models\Images\Package();
        $this->assertInstanceOf('Robinson\Backend\Models\Images\Package', $model);
    }
    
    public function testSavingRecordShouldWorkAsExpected()
    {
        $model = $this->getMockBuilder('Robinson\Backend\Models\Images\Package')
            ->setMethods(array('applyWatermark'))
            ->getMock();
        $model->expects($this->once())
            ->method('applyWatermark')
            ->will($this->returnValue(true));
        
        $model->setImageType(\Robinson\Backend\Models\Images\Images::IMAGE_TYPE_PACKAGE)
            ->setTitle('test image title')
            ->setPackageId(1);
        
        $file = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->enableOriginalClone()
            ->disableOriginalConstructor()
            ->setMethods(array('moveTo', 'getName'))
            ->getMock();
        $file->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(true));
        $file->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('testfile.png'));
        $model->setBasePath(__DIR__)
        ->createFromUploadedFile($file);
        
        $model->save();
        
        $image = \Robinson\Backend\Models\Images\Package::findFirst(array
        (
            'order' => 'sort DESC',
        ));
        
        $this->assertEquals(6, $image->getSort());
        $this->assertEquals('6-testfile.png', $image->getRealFilename());
    }
    
    
}
