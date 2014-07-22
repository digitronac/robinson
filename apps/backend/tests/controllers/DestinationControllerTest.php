<?php
namespace Robinson\Backend\Tests\Controllers;
//@codingStandardsIgnoreStart
class DestinationControllerTest extends BaseTestController
{
    protected $destinationImagesFolder;
    
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('categories');
     //   $this->populateTable('ImageCategory');
        $this->populateTable('destinations');
        $this->populateTable('destination_images');
        $this->populateTable('destination_tabs');
        
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

    public function testIndexActionWithCategoryIdShouldDisplayExpectedCategories()
    {
        $this->registerMockSession();
        $_GET['categoryId'] = 1;
        $this->dispatch('/admin/destination/index');
        $this->assertAction('index');
        $this->assertController('destination');
    }

    public function testIndexActionWithCategoryIdPresetShouldDisplayExpectedCategories()
    {
        $this->registerMockSession();
        $this->getDI()->get('session')->set('categoryId', 1);
        $this->dispatch('/admin/destination/index');
        $this->assertAction('index');
        $this->assertController('destination');
        $this->assertResponseContentContains('<option selected="selected" value="1">fixture category</option>');
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
        
        $categoryId = 1;
        $destination = 'test destination';
        $description = 'test description';
        $status = 1;

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'categoryId' => $categoryId,
            'destination' => $destination,
            'description' => $description,
            'status' => $status,
            'tabs' => array
            (
                \Robinson\Backend\Models\Tabs\Destination::TYPE_APARTMENT => 'Neki lep tekst za apartmane',
                2 => '',
                \Robinson\Backend\Models\Tabs\Destination::TYPE_EXCURSION => 'Neki tekst za ekskurzije?',
            ),
        );

        $this->dispatch('/admin/destination/create');
        /* @var $last \Robinson\Backend\Models\Destination */
        $last = \Robinson\Backend\Models\Destination::findFirst(array
        (
            'order' => 'destinationId DESC',
        ));
        
        // assert tabs
        $this->assertGreaterThan(0, $last->getTabs()->count());
        foreach($last->getTabs() as $tab)
        {
            if ($tab->getType() === \Robinson\Backend\Models\Tabs\Destination::TYPE_APARTMENT)
            {
                $this->assertEquals('Neki lep tekst za apartmane', $tab->getDescription());
            }
            
            if ($tab->getType() === \Robinson\Backend\Models\Tabs\Destination::TYPE_EXCURSION)
            {
                $this->assertEquals('Neki tekst za ekskurzije?', $tab->getDescription());
            }
        }

        $this->assertEquals('fixture-category/test-destination', $last->getSlug());
        
        $this->assertRedirectTo('/admin/destination/update/' . $last->getDestinationId());
    }
    
    public function testUpdatePageShouldShowAsExpected()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/destination/update/4');
        $this->assertAction('update');
        $this->assertController('destination');
        $this->assertResponseCode(200);
        $this->assertResponseContentContains('<textarea id="description" name="description" class="ckeditor" placeholder="Tekst" required="required">description test fixture destination 4</textarea>');
    }
    
    public function testUpdatingDestinationShouldWorkAsExpected()
    {
        $this->registerMockSession();

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'categoryId' => 1,
            'destination' => 'updated destination 4',
            'description' => 'updated description 4',
            'status' => 0,
            'tabs' => array
            (
                \Robinson\Backend\Models\Tabs\Destination::TYPE_APARTMENT => '',
                \Robinson\Backend\Models\Tabs\Destination::TYPE_EXCURSION => 'Neki tekst za ekskurzije?',
                \Robinson\Backend\Models\Tabs\Destination::TYPE_HOTEL => 'Neki gotivan hotel',
            ),
        );

        $this->dispatch('/admin/destination/update/4');
        $destination = \Robinson\Backend\Models\Destination::findFirstByDestinationId(4);
        $this->assertEquals($_POST['categoryId'], $destination->getCategoryId());
        $this->assertEquals($_POST['destination'], $destination->getDestination());
        $this->assertEquals($_POST['description'], $destination->getDescription());
        $this->assertEquals($_POST['status'], $destination->getStatus());
        
        // assert tabs
        $this->assertEquals(2, $destination->getTabs()->count());
        foreach($destination->getTabs() as $tab)
        {
            if ($tab->getType() === \Robinson\Backend\Models\Tabs\Destination::TYPE_HOTEL)
            {
                $this->assertEquals('Neki gotivan hotel', $tab->getDescription());
            }
            
            if ($tab->getType() === \Robinson\Backend\Models\Tabs\Destination::TYPE_EXCURSION)
            {
                $this->assertEquals('Neki tekst za ekskurzije?', $tab->getDescription());
            }
        }

        $this->assertEquals('fixture-category/updated-destination-4', $destination->getSlug());
    }
    
    public function testUpdatingDestinationWithNewImagesShouldWorkAsExpected()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'categoryId' => 1,
            'destination' => 'update destination with image',
            'description' => 'update destination with description',
            'status' => 0,
            'tabs' => array
            (
                1 => '123',
                2 => '456',
                3 => '567',
            ),
        );
        
        $this->registerMockSession();

        // mock stuff for upload
        $fileMock = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->setMethods(array('getName', 'moveTo'))
            ->setMockClassName('MockFileRequest')
            ->disableOriginalConstructor()
            ->getMock();
        $fileMock->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('testfile.png'));
       $fileMock->expects($this->any())
            ->method('moveTo')
            ->will($this->returnValue(true));

        $request = $this->getMock('Phalcon\Http\Request', array('getUploadedFiles'));
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $fileMock,
            )));
        $this->getDI()->set('request', $request, true);

        $destinationImage = $this->getMockBuilder('Robinson\Backend\Models\Images\Destination')
            ->setMethods(array('applyWatermark'))
            ->getMock();
        $destinationImage->expects($this->any())
            ->method('applyWatermark')
            ->will($this->returnValue(true));
        $this->getDI()->set('Robinson\Backend\Models\Images\Destination', $destinationImage);

        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        $this->dispatch('/admin/destination/update/4');
        $this->assertAction('update');
        $this->assertController('destination');
        
        $image = \Robinson\Backend\Models\Images\Destination::findFirstByDestinationId(4);
        $this->assertEquals('6-testfile.png', $image->getRealFileName());
        $this->assertEquals(1, $image->getSort());
        
        $destination = \Robinson\Backend\Models\Destination::findFirst(4);
        
        // assert tabs
        $this->assertEquals(3, $destination->getTabs()->count());
        foreach($destination->getTabs() as $tab)
        {
            foreach($_POST['tabs'] as $tabType => $desc)
            {
                if($tabType === $tab->getType())
                {
                    $this->assertEquals($desc, $tab->getDescription());
                }
            }
        }
    }
    
    public function testAddingImagesToDestinationShouldWorkAsExpected()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'categoryId' => 1,
            'destination' => 'update destination with image',
            'description' => 'update destination with description',
            'status' => 0,
            'tabs' => array
            (
                1 => 'dasdsadsadsa',
                2 => 'dsadsadsdsa',
            ),
        );
        
        $this->registerMockSession();
        
        // mock stuff for upload
        $fileMock = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->setMethods(array('getName', 'moveTo'))
            ->disableOriginalConstructor()
            ->setMockClassName('MockFileRequest')
            ->getMock();
        $fileMock->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('testfile.png'));
       $fileMock->expects($this->any())
            ->method('moveTo')
            ->will($this->returnValue(true));

        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('getUploadedFiles'))
            ->getMock();
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $fileMock,
            )));
        $this->getDI()->set('request', $request, true);

        $destinationImage = $this->getMockBuilder('Robinson\Backend\Models\Images\Destination')
            ->setMethods(array('applyWatermark'))
            ->getMock();
        $destinationImage->expects($this->any())
            ->method('applyWatermark')
            ->will($this->returnValue(true));
        $this->getDI()->set('Robinson\Backend\Models\Images\Destination', $destinationImage);
        
        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
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
        
        $destination = \Robinson\Backend\Models\Destination::findFirst(3);
        
        // assert tabs
        $this->assertEquals(3, $destination->getTabs()->count());
        foreach($destination->getTabs() as $tab)
        {
            foreach($_POST['tabs'] as $tabType => $desc)
            {
                if($tabType === $tab->getType())
                {
                    $this->assertEquals($desc, $tab->getDescription());
                }
            }
        }
    }
    
    public function testReoderingImagesInDestinationShouldWorkAsExpected()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
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
            'tabs' => array
            (
                1 => 'test',
                2 => 'test 2',
                3 => 'test 3',
            ),
            'categoryId' => 1,
            'description' => 'updated description',
            'destination' => 'updated destination',
        );
        
        $this->registerMockSession();

        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        
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
        $_POST = array(
            'id' => 3,
        );

        $this->dispatch('/admin/destination/deleteImage');
        $image = \Robinson\Backend\Models\Images\Destination::findFirst(3);
        $this->assertFalse($image);
    }
    
    public function testEnteringNewTabShouldWorkAsExpected()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'categoryId' => 1,
            'destination' => 'update destination with image',
            'description' => 'update destination with description',
            'status' => 0,
            'tabs' => array
            (
                1 => '123',
                2 => '456',
                3 => '567',
            ),
        );
        
        $this->registerMockSession();

        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        
        $this->getDI()->set('Imagick', $mockImagick);
        $this->dispatch('/admin/destination/update/2');
        $this->assertAction('update');
        $this->assertController('destination');
        
        $destination = \Robinson\Backend\Models\Destination::findFirst(2);
        
        // assert tabs
        $this->assertEquals(3, $destination->getTabs()->count());
        foreach($destination->getTabs() as $tab)
        {
            foreach($_POST['tabs'] as $tabType => $desc)
            {
                if($tabType === $tab->getType())
                {
                    $this->assertEquals($desc, $tab->getDescription());
                }
            }
        }
       
    }
    
    public function testNotEnteringDescriptionForTabWhichDidntExistInFirstPlaceShouldWorkAsExpected()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'categoryId' => 1,
            'destination' => 'update destination with image',
            'description' => 'update destination with description',
            'status' => 0,
            'tabs' => array
            (
                1 => '123',
                2 => '456',
                3 => '',
            ),
        );
        
        $this->registerMockSession();

        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        
        $this->getDI()->set('Imagick', $mockImagick);
        $this->dispatch('/admin/destination/update/2');
        $this->assertAction('update');
        $this->assertController('destination');
        
        $destination = \Robinson\Backend\Models\Destination::findFirst(2);
        
        // assert tabs
        $this->assertEquals(2, $destination->getTabs()->count());
        foreach($destination->getTabs() as $tab)
        {
            foreach($_POST['tabs'] as $tabType => $desc)
            {
                if($tabType === $tab->getType())
                {
                    $this->assertEquals($desc, $tab->getDescription());
                }
            }
        }
    }
}