<?php
namespace Robinson\Backend\Tests\Models;
// @codingStandardsIgnoreStart
class ImagesTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    /**
     * @expectedExceptionMessage imageType must be one of Robinson\Backend\Model\Images\Images.
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
     * @expectedExceptionMessage imageType property must be set prior to calling save.
     */
    public function testCallingSaveWithoutSettingImageTypeShouldRaiseException()
    {
        $this->makeModel()->save();
    }
  
    
    /**
     * 
     * @return \Robinson\Backend\Models\Images\Images
     */
    protected function makeModel()
    {
        return $this->getMockBuilder('Robinson\Backend\Models\Images\Images')->getMockForAbstractClass();
    }
}