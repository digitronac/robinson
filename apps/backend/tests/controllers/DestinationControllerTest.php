<?php
namespace Robinson\Backend\Tests\Controllers;
//@codingStandardsIgnoreStart
class DestinationControllerTest extends BaseTestController
{
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('Category');
     //   $this->populateTable('ImageCategory');
        $this->populateTable('Destinations');
    }
    
    public function testIndexActionShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/destination/index');
        $this->assertAction('index');
        $this->assertController('destination');
    }
    
    public function testCreateActionShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/destination/create');
        $this->assertAction('create');
        $this->assertController('destination');
        
        $this->assertResponseContentContains('<div class="admin destination create">');
    }
    
    public function testCreatingNewDestinationShouldRedirectToUpdateAction()
    {
        $this->registerMockSession();
        
        $request = $this->getMock('Phalcon\Http\Request', array('isPost', 'getPost'));
        $request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        $categoryId = 1;
        $request->expects($this->at(1))
            ->method('getPost')
            ->with($this->equalTo('categoryId'))
            ->will($this->returnValue($categoryId));
        $destination = 'test destination';
        $request->expects($this->at(2))
            ->method('getPost')
            ->with($this->equalTo('destination'))
            ->will($this->returnValue($destination));
        $description = 'test description';
        $request->expects($this->at(3))
            ->method('getPost')
            ->with($this->equalTo('description'))
            ->will($this->returnValue($description));
        $status = 1;
        $request->expects($this->at(4))
            ->method('getPost')
            ->with($this->equalTo('status'))
            ->will($this->returnValue($status));
        
        $this->getDI()->setShared('request', $request);
        $this->dispatch('/admin/destination/create');
        /* @var $last \Robinson\Backend\Models\Destinations */
        $last = \Robinson\Backend\Models\Destinations::findFirst(array
        (
            'order' => 'destinationId DESC',
        ));
        $this->assertRedirectTo('/admin/destination/update/' . $last->getDestinationId());
    }
    
    public function testUpdatePageShouldShowAsExpected()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/destination/update/4');
        $this->assertAction('update');
        $this->assertController('destination');
        $this->assertResponseCode(200);
        $this->assertResponseContentContains('<textarea class="ckeditor" placeholder="Tekst" required="required" name="description" id="description">description test fixture destination 4</textarea>');
    }
    
    public function testUpdatingDestinationShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_POST = array
        (
            'categoryId' => 1,
            'destination' => 'updated destination 4',
            'description' => 'updated description 4',
            'status' => 0,
        );
        $request = $this->getMock('Phalcon\Http\Request', array('isPost'));
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));
        $this->getDI()->setShared('request', $request);
        $this->dispatch('/admin/destination/update/4');
        $destination = \Robinson\Backend\Models\Destinations::findFirstByDestinationId(4);
        $this->assertEquals($_POST['categoryId'], $destination->getCategoryId());
        $this->assertEquals($_POST['destination'], $destination->getDestination());
        $this->assertEquals($_POST['description'], $destination->getDescription());
        $this->assertEquals($_POST['status'], $destination->getStatus());
    }
}