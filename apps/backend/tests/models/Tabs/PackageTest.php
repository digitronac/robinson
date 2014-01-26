<?php
// @codingStandardsIgnoreStart
namespace Robinson\Backend\Tests\Models\Tabs;
class Package extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    public function canMakeModel()
    {
        $package = $this->getDI()->get('Robinson\Backend\Models\Tabs\Package');
        $this->assertInstanceOf('Robinson\Backend\Models\Tabs\Package', $package);
    }
    
    public function testCreatingPackageTabShouldWorkAsExpected()
    {
        $model = new \Robinson\Backend\Models\Tabs\Package();
        $model->setTitle($title = 'test tab title')
            ->setDescription($description = 'test tab description')
            ->setType($type = \Robinson\Backend\Models\Tabs\Package::TYPE_TRIP_PROGRAMME)
            ->setPackageId($packageId = 1);
        $this->assertTrue($model->create());
        
        $packageTab = \Robinson\Backend\Models\Tabs\Package::findFirst(array
        (
            'order' => 'packageTabId DESC',
        ));
        
        $this->assertEquals($title, $packageTab->getTitle());
        $this->assertEquals($description, $packageTab->getDescription());
        $this->assertEquals($type, $packageTab->getType());
        $this->assertEquals($packageId, $packageTab->getDestinationId());
    }
    
    public function testResolveTypeToTitleShouldWorkAsExpected()
    {
        $model = new \Robinson\Backend\Models\Tabs\Package();
        $model->setType(\Robinson\Backend\Models\Tabs\Package::TYPE_TRIP_PROGRAMME);
        $expected = $this->getDI()->getShared('config')->application->package->tabs->toArray()[\Robinson\Backend\Models\Tabs\Package::TYPE_TRIP_PROGRAMME];
        $this->assertEquals($expected, $model->resolveTypeToTitle());
    }
}