<?php
namespace Robinson\Tests\Models;
// @codingStandardsIgnoreStart
class PackageTest extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    protected $pdfFolder;
    
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
    //    $this->populateTable('packages');
        $this->pdfFolder = \org\bovigo\vfs\vfsStream::setup('package/pdf');
        $this->getDI()->getShared('config')->application->packagePdfPath = \org\bovigo\vfs\vfsStream::url('package/pdf');
    }
    
    public function testCanCreateModel()
    {
        $model = $this->getDI()->get('Robinson\Backend\Models\Package');
        $this->assertInstanceOf('Robinson\Backend\Models\Package', $model);
    }
    
    public function testCreatingNewRecordShouldWorkAsExpected()
    {
        $file = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'moveTo'))
            ->getMock();
        $file->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('mypdftestname.pdf'));
        $file->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(true));
        
        $destination = $this->getDI()->get('Robinson\Backend\Models\Destination');
        $destination = $destination->findFirst(1);
        /* @var $model \Robinson\Backend\Models\Package */
        $model = $this->getDI()->get('Robinson\Backend\Models\Package');
        $model->setPackage($packageName = 'ourtestpackage');
        $model->setDescription($description = 'our package description');
        $model->setPrice($price = 55);
        $model->setDestination($destination);
        $model->setUploadedPdf($file);
        $model->setSpecial('2014-06-11');
        $this->assertTrue($model->create());

        /* @var $package \Robinson\Backend\Models\Package */
        $package = \Robinson\Backend\Models\Package::findFirst(array
        (
            'package = "' . $packageName . '"',
        ));
        
        $this->assertInstanceOf('Robinson\Backend\Models\Package', $package);
        $this->assertEquals($packageName, $package->getPackage());
        $this->assertEquals($description, $package->getDescription());
        $this->assertEquals($price, $package->getPrice());
        $this->assertEquals('2014-06-11', $package->getSpecial());
    }
}