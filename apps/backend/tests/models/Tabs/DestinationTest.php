<?php
// @codingStandardsIgnoreStart
namespace Robinson\Backend\Tests\Models\Tabs;
class Destination extends \Robinson\Backend\Tests\Models\BaseTestModel
{
    public function testCanCreateModel()
    {
        $model = $this->getDI()->get('Robinson\Backend\Models\Tabs\Destination');
        $this->assertInstanceOf('Robinson\Backend\Models\Tabs\Destination', $model);
    }
    
    public function testCreatingDestinationTabShouldWorkAsExpected()
    {
        $model = new \Robinson\Backend\Models\Tabs\Destination();
        $model->setTitle($title = 'test tab title')
            ->setDescription($description = 'test tab description')
            ->setType($type = \Robinson\Backend\Models\Tabs\Destination::TYPE_APARTMENT)
            ->setDestinationId($destinationId = 1);
        $this->assertTrue($model->create());
        
        $destinationTab = \Robinson\Backend\Models\Tabs\Destination::findFirst(array
        (
            'order' => 'destinationId DESC',
        ));
        
        $this->assertEquals($title, $destinationTab->getTitle());
        $this->assertEquals($description, $destinationTab->getDescription());
        $this->assertEquals($type, $destinationTab->getType());
        $this->assertEquals($destinationId, $destinationTab->getDestinationId());
    }
    
    public function testResolveTypeToTitleShouldWorkAsExpected()
    {
        $model = new \Robinson\Backend\Models\Tabs\Destination();
        $model->setType(\Robinson\Backend\Models\Tabs\Destination::TYPE_APARTMENT);
        $expected = $this->getDI()->getShared('config')->application->destination->tabs->toArray()[\Robinson\Backend\Models\Tabs\Destination::TYPE_APARTMENT];
        $this->assertEquals($expected, $model->resolveTypeToTitle());
    }
}