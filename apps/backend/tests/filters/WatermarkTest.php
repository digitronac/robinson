<?php
namespace Robinson\Backend\Tests\Filter;
// @codingStandardsIgnoreStart
class WatermarkTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    protected $vfs;
    
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        
        $this->vfs = \org\bovigo\vfs\vfsStream::setup('root', null, array
        (
            'img' => array
            (
                'assets' => array
                (
                    'watermark.png' => file_get_contents($this->getDI()->getShared('config')->application->watermark->watermark),
                    'image-to-be-watermarked.jpg' => file_get_contents(APPLICATION_PATH . '/backend/tests/_setup/watermark/image-to-be-watermarked.jpg'),
                ),
            ),
        ));

        $this->getDI()->getShared('config')->application->watermark->watermark = \org\bovigo\vfs\vfsStream::url('root/img/assets/watermark.png');
    }
    
    public function testCreatingFilterShouldWork()
    {
        /* @var $watermark \Robinson\Backend\Filter\Watermark */
        $watermark = $this->getDI()->get('Robinson\Backend\Filter\Watermark', array
        (
            new \Imagick($this->getDI()->getShared('config')->application->watermark->watermark))
        );
        
        $this->assertInstanceOf('Robinson\Backend\Filter\Watermark', $watermark);
    }
    
    public function testCallingFilterShouldCreatedWatermarkedImage()
    {
        $expectedWatermarkedFile = APPLICATION_PATH . '/backend/tests/_setup/watermark/expected_watermarked.jpg';
        $watermark = $this->makeImagickWatermark();
        $imagick = $this->getMockBuilder('Imagick')
            ->setMethods(array('getfilename'))
            ->getMock();
        $imagick->expects($this->once())
            ->method('getfilename')
            ->will($this->returnValue($expectedWatermarkedFile));
        
        $imagick->readimageblob(file_get_contents($this->getMockImageFileToBeWatermarked()));
        $originalsize = strlen(file_get_contents($this->getMockImageFileToBeWatermarked()));
        $watermark->filter($imagick);
        $newsize = strlen(file_get_contents($expectedWatermarkedFile));
        $this->assertTrue(is_file($expectedWatermarkedFile));
        unlink($expectedWatermarkedFile);
        $this->assertGreaterThan($originalsize, $newsize);
        
    }
    
    /**
     * 
     * @return \Robinson\Backend\Filter\Watermark
     */
    protected function makeImagickWatermark()
    {
        /* @var $watermark \Robinson\Backend\Filter\Watermark */
        return $this->getDI()->get('Robinson\Backend\Filter\Watermark', array
        (
            new \Imagick($this->getDI()->getShared('config')->application->watermark->watermark))
        );
    }
    
    protected function getMockImageFileToBeWatermarked()
    {
        return \org\bovigo\vfs\vfsStream::url('root/img/assets/image-to-be-watermarked.jpg');
    }
}
