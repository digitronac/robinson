<?php
/*namespace Robinson\Backend\Tests\Controllers;
// @codingStandardsIgnoreStart
class PackageControllerTest extends \Robinson\Backend\Tests\Controllers\BaseTestController
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
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
*/