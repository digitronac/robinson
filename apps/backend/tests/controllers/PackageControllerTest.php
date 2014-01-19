<?php
namespace Robinson\Backend\Tests\Controllers;
// @codingStandardsIgnoreStart
class PackageControllerTest extends \Robinson\Backend\Tests\Controllers\BaseTestController
{
    protected $pdfFolder;
    
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('packages');
        $this->pdfFolder = \org\bovigo\vfs\vfsStream::setup('pdf/package');
        $this->getDI()->getShared('config')->application->packagePdfPath = \org\bovigo\vfs\vfsStream::url('pdf/package');
    }
    
    public function testIndexPackageActionShouldExist()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/package/index');
        $this->assertAction('index');
        $this->assertController('package');
    }
    
    public function testCreatePackageActionShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/package/create');
        $this->assertAction('create');
        $this->assertController('package');
        
        $package = \Robinson\Backend\Models\Package::findFirst(array
        (
            'order' => 'packageId DESC',
        ));
        
        $this->assertInstanceOf('Robinson\Backend\Models\Package', $package);
    }
    
    public function testCreatingNewPackageShouldBeInsertInDbWithPdfUploaded()
    {
        $this->registerMockSession();
        $_POST = array
        (
            'destinationId' => 1,
            'package' => 'test package name :)',
            'description' => 'test package description :)',
            'price' => 99,
            'status' => 0,
        );
        
        // mock stuff for upload
        
        $request = $this->getMock('Phalcon\Http\Request', array('isPost', 'getUploadedFiles'));
        $request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        
        $fileMock = $this->getMock('Phalcon\Http\Request\File', array('getName', 'moveTo'), array(), 'MockFileRequest', false);
        $fileMock->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('prices.pdf'));
       $fileMock->expects($this->any())
            ->method('moveTo')
            ->will($this->returnValue(true));
        
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $fileMock,
            )));
        
       $this->getDI()->setShared('request', $request);
       
       $this->dispatch('/admin/package/create');
       $this->assertAction('create');
       $this->assertRedirectTo('/admin/package/update/6');
    }
    
    public function testUpdatePackageActionShouldExist()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/package/update/1');
        $this->assertAction('update');
        $this->assertController('package');
    }
    
    public function testDeleteImageShouldExist()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/package/deleteImage/1');
        $this->assertAction('deleteImage');
        $this->assertController('package');
    }
    
    public function testDeletePdfActionShouldExist()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/package/deletePdf/1');
        $this->assertAction('deletePdf');
        $this->assertController('package');
    }
}
