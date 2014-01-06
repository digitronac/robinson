<?php
namespace Robinson\Backend\Tests\Models;
class ImageCategoryTest extends \Phalcon\Test\UnitTestCase
{
    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage Unable to save ImageCategory model.
     */
    public function testCreateFromFileWithUnsuccessfulSaveShouldThrowError()
    {
        /* @var $mockImageCategory \Robinson\Backend\Models\ImageCategory */
        $mockImageCategory = $this->getMock('Robinson\Backend\Models\ImageCategory', array('save'), array('/'));
        $mockImageCategory->expects($this->once())
            ->method('save')
            ->will($this->returnValue(false));
        $mockFileRequest = $this->getMock('Phalcon\Http\Request\File', array(), array('fakefile.jpg'), 'Mock_Request_File', false);
        $mockImageCategory->setBasePath('.');
        $mockImageCategory->createFromUploadedFile($mockFileRequest, 1);  
    }
    
    /**
     * @expectedException \Phalcon\Mvc\Model\Exception
     * @expectedExceptionMessage basePath is not set.
     */
    public function testNoBasePathSetShouldThrowException()
    {/* @var $mockImageCategory \Robinson\Backend\Models\ImageCategory */
        $mockImageCategory = $this->getMock('Robinson\Backend\Models\ImageCategory', array('save'), array('/'));
        $mockFileRequest = $this->getMock('Phalcon\Http\Request\File', array(), array('fakefile.jpg'), 'Mock_Request_File', false);
        $mockImageCategory->createFromUploadedFile($mockFileRequest, 1);  
    }
    
    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage Unable to move uploaded file to destination "./0-n-a."
     */
    public function testUnableToMoveFileShouldThrowException()
    {
        /* @var $mockImageCategory \Robinson\Backend\Models\ImageCategory */
        $mockImageCategory = $this->getMock('Robinson\Backend\Models\ImageCategory', array('save', 'makeFileObject'));
        $mockImageCategory->setBasePath('.');
        $mockImageCategory->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        
        $mockFileRequest = $this->getMock('Phalcon\Http\Request\File', array('moveTo', 'save'), array('fakefile.jpg'), 'Mock_Request_File', false);
        
        $mockFileRequest->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(false));
        $mockImageCategory->createFromUploadedFile($mockFileRequest, 1);  
    }
    
    /**
     * @expectedException \Phalcon\Mvc\Model\Exception
     * @expectedExceptionMessage basePath is not set.
     */
    public function testDeleteFileWithoutSetBasePathShouldThrowException()
    {
        $imageCategory = new \Robinson\Backend\Models\ImageCategory();
        $imageCategory->delete();
    }
    
    public function testDeleteFileShouldBehaveAsExpected()
    {
        $mockImageCategory = $this->getMock('Robinson\Backend\Models\ImageCategory', array('isFile', 'unlink', 'parentDelete'));
        $mockImageCategory->setBasePath('.');
        $mockImageCategory->expects($this->any())
            ->method('isFile')
            ->will($this->returnValue(true));
        $mockImageCategory->expects($this->any())
            ->method('unlink')
            ->will($this->returnValue(true));
        $mockImageCategory->expects($this->any())
            ->method('parentDelete')
            ->will($this->returnValue(true));
        $this->assertTrue($mockImageCategory->delete());
    }
    
    public function testCallingGetResizedSrcBehavesAsExpected()
    {
        $mockImageCategory = $this->getMock('Robinson\Backend\Models\ImageCategory', array('isDir', 'mkdir', 'getRealFilename'));
        $mockImageCategory->expects($this->once())
            ->method('isDir')
            ->with($this->anything())
            ->will($this->returnValue(false));
        $mockImageCategory->expects($this->once())
            ->method('mkdir')
            ->with($this->anything())
            ->will($this->returnValue(true));
        $mockImageCategory->expects($this->exactly(3))
            ->method('getRealFilename')
            ->will($this->returnValue('test-file.jpg'));
        
        /* @var $mockImagick \Imagick */
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $this->getDI()->set('Imagick', $mockImagick);
        $this->assertEquals('/img/category/300x0/test-file.jpg', $mockImageCategory->getResizedSrc(300, 0));
    }
    
    public function testCallingGetResizedSrcWithExistingCropFileBehavesAsExpected()
    {
        $mockImageCategory = $this->getMock('Robinson\Backend\Models\ImageCategory', array('isDir', 'isFile', 'mkdir', 'getRealFilename'));
        $mockImageCategory->expects($this->once())
            ->method('isDir')
            ->with($this->anything())
            ->will($this->returnValue(false));
        $mockImageCategory->expects($this->once())
            ->method('mkdir')
            ->with($this->anything())
            ->will($this->returnValue(true));
        $mockImageCategory->expects($this->exactly(2))
            ->method('getRealFilename')
            ->will($this->returnValue('test-file.jpg'));
        $mockImageCategory->expects($this->once())
            ->method('isFile')
            ->will($this->returnValue(true));
        
        /* @var $mockImagick \Imagick */
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $this->getDI()->set('Imagick', $mockImagick);
        $this->assertEquals('/img/category/300x0/test-file.jpg', $mockImageCategory->getResizedSrc(300, 0));
    }
}