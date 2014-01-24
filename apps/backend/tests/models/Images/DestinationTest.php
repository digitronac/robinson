<?php
namespace Robinson\Backend\Tests\Models;
// @codingStandardsIgnoreStart
class DestinationTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
  
        $this->populateTable('destinations');
        $this->populateTable('destination_images');
    }
    
    public function testCreatingModelShouldCreateDestinationObject()
    {
        $model = $this->getDI()->get('Robinson\Backend\Models\Images\Destination');
        $this->assertInstanceOf('Robinson\Backend\Models\Images\Destination', $model);
    }
    
    /**
     * @expectedExceptionMessage imageType must be one of Robinson\Backend\Models\Images\Images.
     * @expectedException Robinson\Backend\Models\Images\Exception
     */
    public function testSettingWrongImageTypeShouldRaiseException()
    {
        $this->makeModel()->setImageType('wrong');
    }
    
    /**
     * @expectedExceptionMessage basePath is not set.
     * @expectedException Robinson\Backend\Models\Images\Exception 
     */
    public function testBasePathNotBeingSetRaisesException()
    {
        $this->di->remove('config');
      
        $file = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->enableOriginalClone()
            ->disableOriginalConstructor()
            ->getMock();
        $this->makeModel()->createFromUploadedFile($file);
    }

    /**
     * @expectedException Robinson\Backend\Models\Images\Exception
     * @expectedExceptionMessage Unable to move uploaded file to destination
     */
    public function testNotBeingAbleToMoveUploadedFileShouldRaiseException()
    {
        $model = $this->makeModel();
        $model->setImageType(\Robinson\Backend\Models\Images\Images::IMAGE_TYPE_DESTINATION)
            ->setSort(1)
            ->setDestinationId(1);
        
        $file = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->enableOriginalClone()
            ->disableOriginalConstructor()
            ->setMethods(array('moveTo', 'getName'))
            ->getMock();
        $file->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(false));
        $file->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('testfile.png'));
        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        $model->setBasePath(__DIR__)
        ->createFromUploadedFile($file)
        ->save();
    }
    
    /**
     * @expectedException Robinson\Backend\Models\Images\Exception
     * @expectedExceptionMessage Unable to save destination image model.
     */
    public function testFailingInternalSaveShouldRaiseException()
    {
        $model = $this->getMockBuilder('Robinson\Backend\Models\Images\Destination')
            ->setMethods(array('parentSave'))
            ->getMockForAbstractClass();
        $model->expects($this->once())
            ->method('parentSave')
            ->will($this->returnValue(false));
        $model->setImageType(\Robinson\Backend\Models\Images\Images::IMAGE_TYPE_DESTINATION)
            ->setSort(1);
        $model->save();
    }
    
    public function testSavingRecordShouldWorkAsExpected()
    {
        $this->truncateTable('destination_images');
        $model = $this->makeModel();
        $model->setImageType(\Robinson\Backend\Models\Images\Images::IMAGE_TYPE_DESTINATION)
            ->setSort(1)
            ->setDestinationId(1);
        
        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        
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
        ->createFromUploadedFile($file)
        ->save();
        
        $image = \Robinson\Backend\Models\Images\Destination::findFirst(array
        (
            'order' => 'sort DESC',
        ));
        
        $this->assertEquals(1, $image->getSort());
        $this->assertEquals('1-testfile.png', $image->getRealFilename());
    }
    
    /**
     * 
     * @return \Robinson\Backend\Models\Images\Destination 
     */
    protected function makeModel()
    {
        $model = $this->getMockBuilder('Robinson\Backend\Models\Images\Destination')
            ->setMethods(array('applyWatermark'))
            ->getMock();
        $model->expects($this->any())
            ->method('applyWatermark')
            ->will($this->returnValue(true));
        return $model;
    }
}