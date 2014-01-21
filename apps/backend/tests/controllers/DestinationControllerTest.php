<?php
namespace Robinson\Backend\Tests\Controllers;
//@codingStandardsIgnoreStart
class DestinationControllerTest extends BaseTestController
{
    protected $destinationImagesFolder;
    
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('Category');
     //   $this->populateTable('ImageCategory');
        $this->populateTable('Destinations');
        $this->populateTable('DestinationImages');
        
        // setup fs
        $this->destinationImagesFolder = \org\bovigo\vfs\vfsStream::setup('img/destination');
        $this->getDI()->getShared('config')->application->destinationImagesPath = \org\bovigo\vfs\vfsStream::url('img/destination');
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
    
    public function testUpdatingDestinationWithNewImagesShouldWorkAsExpected()
    {
        $_POST = array
        (
            'categoryId' => 1,
            'destination' => 'update destination with image',
            'description' => 'update destination with description',
            'status' => 0,
        );
        
        $this->registerMockSession();
        $request = $this->getMock('Phalcon\Http\Request', array('isPost', 'getUploadedFiles'));
        $request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        
        // mock stuff for upload
        
        $fileMock = $this->getMock('Phalcon\Http\Request\File', array('getName', 'moveTo'), array(), 'MockFileRequest', false);
        $fileMock->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('testfile.png'));
       $fileMock->expects($this->any())
            ->method('moveTo')
            ->will($this->returnValue(true));
        
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $fileMock,
            )));
        
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        
      
        $this->getDI()->setShared('request', $request);
        
        $this->getDI()->set('Imagick', $mockImagick);
        $this->dispatch('/admin/destination/update/4');
        $this->assertAction('update');
        $this->assertController('destination');
        
        $image = \Robinson\Backend\Models\Images\Destination::findFirstByDestinationId(4);
        $this->assertEquals('6-testfile.png', $image->getRealFileName());
        $this->assertEquals(1, $image->getSort());
    }
    
    public function testAddingImagesToDestinationShouldWorkAsExpected()
    {
        $_POST = array
        (
            'categoryId' => 1,
            'destination' => 'update destination with image',
            'description' => 'update destination with description',
            'status' => 0,
        );
        
        $this->registerMockSession();
        $request = $this->getMock('Phalcon\Http\Request', array('isPost', 'getUploadedFiles'));
        $request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        
        // mock stuff for upload
        
        $fileMock = $this->getMock('Phalcon\Http\Request\File', array('getName', 'moveTo'), array(), 'MockFileRequest', false);
        $fileMock->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('testfile.png'));
       $fileMock->expects($this->any())
            ->method('moveTo')
            ->will($this->returnValue(true));
        
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $fileMock,
            )));
        
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        
        $this->getDI()->setShared('request', $request);
        
        $this->getDI()->set('Imagick', $mockImagick);
        $this->dispatch('/admin/destination/update/3');
        $this->assertAction('update');
        $this->assertController('destination');
        
        $image = \Robinson\Backend\Models\Images\Destination::findFirst(array
        (
            'destinationId' => 3,
            'order' => 'destinationImageId DESC',
        ));
        $this->assertEquals('6-testfile.png', $image->getRealFileName());
        $this->assertEquals(6, $image->getSort());
    }
    
    public function testReoderingImagesInDestinationShouldWorkAsExpected()
    {
        $_POST = array
        (
            'sort' => array
            (
                5 => 1,
                4 => 2,
                3 => 3,
                2 => 4,
                1 => 5,
            ),
        );
        
        $this->registerMockSession();
        $request = $this->getMock('Phalcon\Http\Request', array('isPost'));
        $request->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        $this->getDI()->set('Imagick', $mockImagick);
        $this->getDI()->setShared('request', $request);
        
        
        $this->dispatch('/admin/destination/update/3');
        
        $images = \Robinson\Backend\Models\Images\Destination::findByDestinationId(3);
        $this->assertCount(5, $images);
        foreach($images as $image)
        {
            $this->assertEquals($_POST['sort'][$image->getImageId()], $image->getSort());
        }
    }
    
    public function testDeletingDestinationImageShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $requestMock = $this->getMock('Phalcon\Http\Request', array('getPost'));
        $requestMock->expects($this->once())
            ->method('getPost')
            ->with($this->equalTo('id'))
            ->will($this->returnValue(3));
        $this->getDI()->setShared('request', $requestMock);
        $this->dispatch('/admin/destination/deleteImage');
        $image = \Robinson\Backend\Models\Images\Destination::findFirst(3);
        $this->assertFalse($image);
    }
    
    
}