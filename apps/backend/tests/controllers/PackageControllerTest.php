<?php
namespace Robinson\Backend\Tests\Controllers;
// @codingStandardsIgnoreStart
class PackageControllerTest extends \Robinson\Backend\Tests\Controllers\BaseTestController
{
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('packages');
        $this->populateTable('package_images');
        $this->populateTable('package_tabs');
        $this->populateTable('package_tags');

        \org\bovigo\vfs\vfsStream::setup('root', null, array
        (
            'pdf' => array
            (
                'package' => array(),
            ),
            'img' => array
            (
                'package' => array(),
            ),
        ));

        $this->getDI()->get('config')->application->packagePdfPath = \org\bovigo\vfs\vfsStream::url('root/pdf/package');
        $this->getDI()->get('config')->application->packageImagesPath = \org\bovigo\vfs\vfsStream::url('root/img/package');
    }
    
    public function testIndexPackageActionShouldExist()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/package/index');
        $this->assertAction('index');
        $this->assertController('package');
    }

    public function testIndexPackageActionWithDestinationIdShouldDisplayExpectedPackages()
    {
        $this->registerMockSession();
        $_GET['destinationId'] = 1;
        $this->dispatch('/admin/package/index');
        $this->assertAction('index');
        $this->assertController('package');
        $document = new \DOMDocument('1.0', 'utf-8');
        $document->loadHTML($this->getContent());
        $xpath = new \DOMXPath($document);
        $this->assertEquals(5, $xpath->query('//div[@class="admin package index"]/div/ul/li')->length);
    }

    public function testIndexActionWithDestinationIdPresetShouldDisplayExpectedPackages()
    {
        $this->registerMockSession();
        $this->getDI()->get('session')->set('destinationId', 1);
        $this->dispatch('/admin/package/index');
        $this->assertAction('index');
        $this->assertController('package');
        $this->assertResponseContentContains('<option selected="selected" value="1">fixture destination 1</option>');
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
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 1,
            'package' => 'test package name :)',
            'description' => 'test package description :)',
            'price' => 99,
            'status' => 0,
            'tabs' => array
            (
                1 => 'trlalalala',
                2 => 'test text',
                3 => 'test 3 text',
            ),
            'tags' => array
            (
                1 => 'First minute',
                2 => 'Last minute',
            ),
            'special' => '2014-06-12',
        );
        
        // mock stuff for upload
        $fileMock = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->setMethods(array('getName', 'moveTo', 'getKey'))
            ->disableOriginalConstructor()
            ->getMock();

        $fileMock->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('prices.pdf'));
        $fileMock->expects($this->once())
            ->method('getKey')
            ->will($this->returnValue('pdf'));
       $fileMock->expects($this->any())
            ->method('moveTo')
            ->will($this->returnValue(true));

        $request = $this->getMock('Phalcon\Http\Request', array('getUploadedFiles'));
        $request->expects($this->exactly(2))
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $fileMock,
            )));
       $this->getDI()->set('request', $request, true);

       $this->dispatch('/admin/package/create');
       $this->assertAction('create');
       $this->assertRedirectTo('/admin/package/update/6');
       
        /* @var $last \Robinson\Backend\Models\Package */
        $last = \Robinson\Backend\Models\Package::findFirst(array
        (
            'order' => 'packageId DESC',
        ));
        
        // assert tabs
        $this->assertGreaterThan(0, $last->getTabs()->count());
        foreach ($last->getTabs() as $tab)
        {
             if ($tab->getType() === \Robinson\Backend\Models\Tabs\Package::TYPE_PROGRAMME)
             {
                 $this->assertEquals('trlalalala', $tab->getDescription());
             }

             if ($tab->getType() === \Robinson\Backend\Models\Tabs\Package::TYPE_CONDITIONS)
             {
                 $this->assertEquals('test text', $tab->getDescription());
             }
             
             if ($tab->getType() === \Robinson\Backend\Models\Tabs\Package::TYPE_AVIO)
             {
                 $this->assertEquals('test 3 text', $tab->getDescription());
             }
        }

        $this->assertCount(2, $last->getTags());
        foreach ($last->getTags() as $tag)
        {
            foreach ($_POST['tags'] as $type => $title)
            {
                if($tag->getType() === $type)
                {
                    $this->assertEquals($title, $tag->getTag());
                }
            }
        }

        $this->assertEquals('fixture-category/fixture-destination-1/test-package-name', $last->getSlug());
    }

    public function testCreatingNewPackageShouldBeInsertInDbWithBothPdfsUploadedAndTabsAndTagsWithoutDescriptionShouldBeSkipped()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 1,
            'package' => 'test package name :)',
            'description' => 'test package description :)',
            'price' => 99,
            'status' => 0,
            'tabs' => array
            (
                1 => 'trlalalala',
                2 => 'test text',
                3 => '',
            ),
            'tags' => array
            (
                1 => 'First minute',
                2 => '',
            ),
        );

        // mock stuff for upload
        $fileMock = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->setMethods(array('getName', 'moveTo', 'getKey'))
            ->disableOriginalConstructor()
            ->getMock();

        $fileMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('prices.pdf'));
        $fileMock->expects($this->any())
            ->method('getKey')
            ->will($this->returnValue('pdf'));
        $fileMock->expects($this->any())
            ->method('moveTo')
            ->will($this->returnValue(true));

        $request = $this->getMock('Phalcon\Http\Request', array('getUploadedFiles'));
        $request->expects($this->any())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $fileMock,
            )));

        $this->getDI()->set('request', $request, true);
        $this->dispatch('/admin/package/create');
        $this->assertAction('create');
        $this->assertRedirectTo('/admin/package/update/6');

        /* @var $last \Robinson\Backend\Models\Package */
        $last = \Robinson\Backend\Models\Package::findFirst(array
            (
                'order' => 'packageId DESC',
            ));

        // assert tabs
        $this->assertGreaterThan(0, $last->getTabs()->count());
        foreach ($last->getTabs() as $tab)
        {
            if ($tab->getType() === \Robinson\Backend\Models\Tabs\Package::TYPE_PROGRAMME)
            {
                $this->assertEquals('trlalalala', $tab->getDescription());
            }

            if ($tab->getType() === \Robinson\Backend\Models\Tabs\Package::TYPE_CONDITIONS)
            {
                $this->assertEquals('test text', $tab->getDescription());
            }

            if ($tab->getType() === \Robinson\Backend\Models\Tabs\Package::TYPE_AVIO)
            {
                $this->assertEquals('', $tab->getDescription());
            }
        }

        $this->assertCount(1, $last->getTags());
        foreach ($last->getTags() as $tag)
        {
            foreach ($_POST['tags'] as $type => $title)
            {
                if($tag->getType() === $type)
                {
                    $this->assertEquals($title, $tag->getTag());
                }
            }
        }
    }
    
    public function testUpdatePackageActionShouldExist()
    {
        $this->registerMockSession();

        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        $this->dispatch('/admin/package/update/1');
        $this->assertAction('update');
        $this->assertController('package');
    }
    
    public function testUpdatePackageWithNewImageTabsAndTagsShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 999,
            'status' => 1,
            'tabs' => array
            (
                1 => 'test 1',
                2 => 'test 2',
            ),
            'tags' => array
            (
                1 => 'First minute',
                2 => 'Last minute',
            ),
            'special' => '2014-06-11',
        );
        
        $mockImageFile = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'moveTo'))
            ->getMock();
        $mockImageFile->expects($this->exactly(3))
            ->method('getName')
            ->will($this->returnValue('packageimagetest.jpg'));
        $mockImageFile->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(true));
        
        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('getUploadedFiles'))
            ->getMock();
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $mockImageFile,
            )));
        $this->getDI()->set('request', $request, true);

        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        
        $packageImage = $this->getMockBuilder('Robinson\Backend\Models\Images\Package')
            ->setMethods(array('applyWatermark'))
            ->getMock();
        $packageImage->expects($this->any())
            ->method('applyWatermark')
            ->will($this->returnValue(true));
        $this->getDI()->set('Robinson\Backend\Models\Images\Package', $packageImage);

        $this->dispatch('/admin/package/update/1');
        
        $package = \Robinson\Backend\Models\Package::findFirst();
        $this->assertEquals(2, $package->getDestination()->getDestinationId());
        $this->assertEquals('test package name 2 :)', $package->getPackage());
        $this->assertEquals('test package description 2 :)', $package->getDescription());
        $this->assertEquals(999, $package->getPrice());
        $this->assertEquals(\Robinson\Backend\Models\Package::STATUS_VISIBLE, $package->getStatus());
        $image = \Robinson\Backend\Models\Images\Package::findFirst(6);
        $this->assertEquals('6-packageimagetest.jpg', $image->getRealFileName());
        $this->assertEquals('packageimagetest.jpg', $image->getTitle());
        $this->assertEquals('fixture-category/fixture-destination-2/test-package-name-2', $package->getSlug());

        // assert tabs
        $this->assertGreaterThan(0, $package->getTabs()->count());
        
        foreach ($package->getTabs() as $tab)
        {
             foreach ($_POST['tabs'] as $type => $description)
             {
                 if($tab->getType() === $type)
                 {
                     $this->assertEquals($description, $tab->getDescription());
                 }
             }
         }

        // tag check
        foreach ($package->getTags() as $tag)
        {
            foreach ($_POST['tags'] as $type => $title)
            {
                if($tag->getType() === $type)
                {
                    $this->assertEquals($title, $tag->getTag());
                }
            }
        }
    }
    
    public function testUpdatePackageWithNewPdfAndTabsShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 999,
            'status' => 1,
            'tabs' => array
            (
                1 => 'tab1',
                3 => 'tab3',
                4 => 'tab4',
            ),
        );
        
        $mockPdfFile = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'moveTo', 'getKey'))
            ->getMock();
        $mockPdfFile->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('packagepdftest.pdf'));
        $mockPdfFile->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(true));
        $mockPdfFile->expects($this->once())
            ->method('getKey')
            ->will($this->returnValue('pdf'));
        
        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('isPost', 'getUploadedFiles'))
            ->getMock();
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $mockPdfFile,
            )));
         
       
        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        
        $this->getDI()->set('request', $request, true);
        $this->dispatch('/admin/package/update/1');
        
        $package = \Robinson\Backend\Models\Package::findFirst();
        $this->assertEquals(2, $package->getDestination()->getDestinationId());
        $this->assertEquals('test package name 2 :)', $package->getPackage());
        $this->assertEquals('test package description 2 :)', $package->getDescription());
        $this->assertEquals(999, $package->getPrice());
        $this->assertEquals(\Robinson\Backend\Models\Package::STATUS_VISIBLE, $package->getStatus());
        $this->assertEquals('packagepdftest.pdf', $package->getPdf());
        
        // assert tabs
        $this->assertGreaterThan(0, $package->getTabs()->count());
        
        foreach ($package->getTabs() as $tab)
        {
             foreach ($_POST['tabs'] as $type => $description)
             {
                 if($tab->getType() === $type)
                 {
                     $this->assertEquals($description, $tab->getDescription());
                 }
             }
         }
    }

    public function testUpdatePackageWithUpdatedPdfShouldWorkAsExpectedAndClearObsoleteFiles()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 999,
            'status' => 1,
            'tabs' => array
            (
                1 => 'tab1',
                3 => 'tab3',
                4 => 'tab4',
            ),
        );

        $mockPdfFile = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'moveTo', 'getKey'))
            ->getMock();
        $mockPdfFile->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('packagepdftest.pdf'));
        $mockPdfFile->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(true));
        $mockPdfFile->expects($this->once())
            ->method('getKey')
            ->will($this->returnValue('pdf'));

        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('getUploadedFiles'))
            ->getMock();
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
                (
                    0 => $mockPdfFile,
                )));
        $this->getDI()->set('request', $request, true);

        $this->getDI()->set('Imagick', $this->mockWorkingImagick());


        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods(array('getPathname', 'getFilename'))
            ->getMock();
        $file->expects($this->exactly(1))
            ->method('getPathname')
            ->will($this->onConsecutiveCalls('packagepdftest.pdf', 'packagepdftest.html'));
        $file->expects($this->exactly(2))
            ->method('getFilename')
            ->will($this->onConsecutiveCalls('packagepdftest.pdf', 'packagepdftest.html'));


        $iterator = $this->getMockBuilder('Symfony\Component\Finder\Iterator\PathFilterIterator')
            ->setMethods(array('valid', 'current'))
            ->setConstructorArgs(array(new \ArrayIterator(array()), array(), array()))
            ->getMock();
        $iterator->expects($this->any())
            ->method('current')
            ->will($this->returnValue($file));
        $iterator->expects($this->any())
            ->method('valid')
            ->will($this->onConsecutiveCalls(true, true, false));

        $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->setMethods(array('getIterator'))
            ->getMock();
        $finder->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->getDI()->set('Symfony\Component\Finder\Finder', $finder);

        // mock filesystem component
        $filesystem = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
            ->setMethods(array('remove'))
            ->getMock();
        $filesystem->expects($this->once())
            ->method('remove')
            ->will($this->returnValue(true));
        $this->getDI()->set('Symfony\Component\Filesystem\Filesystem', $filesystem);

        $this->dispatch('/admin/package/update/1');

        $package = \Robinson\Backend\Models\Package::findFirst();
        $this->assertEquals(2, $package->getDestination()->getDestinationId());
        $this->assertEquals('test package name 2 :)', $package->getPackage());
        $this->assertEquals('test package description 2 :)', $package->getDescription());
        $this->assertEquals(999, $package->getPrice());
        $this->assertEquals(\Robinson\Backend\Models\Package::STATUS_VISIBLE, $package->getStatus());
        $this->assertEquals('packagepdftest.pdf', $package->getPdf());

        // assert tabs
        $this->assertGreaterThan(0, $package->getTabs()->count());

        foreach ($package->getTabs() as $tab)
        {
            foreach ($_POST['tabs'] as $type => $description)
            {
                if($tab->getType() === $type)
                {
                    $this->assertEquals($description, $tab->getDescription());
                }
            }
        }
    }

    /**
     * @expectedException \Phalcon\Exception
     * @expectedExceptionMessage price is required
     */
    public function testFailureToUpdatePackageBySettingPriceAsStringShouldResultInException()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 'dsadasdsadsa',
            'status' => 1,
            'tabs' => array
            (
                1 => 'tab1',
                3 => 'tab3',
                4 => 'tab4',
            ),
        );

        $mockPdfFile = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'moveTo', 'getKey'))
            ->getMock();
        $mockPdfFile->expects($this->exactly(1))
            ->method('getName')
            ->will($this->returnValue('packagepdftest.pdf'));
        $mockPdfFile->expects($this->any())
            ->method('moveTo')
            ->will($this->returnValue(true));
        $mockPdfFile->expects($this->any())
            ->method('getKey')
            ->will($this->returnValue('pdf'));

        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('getUploadedFiles'))
            ->getMock();
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $mockPdfFile,
            )));
        $this->getDI()->set('request', $request, true);

        $this->getDI()->set('Imagick', $this->mockWorkingImagick());


        $this->dispatch('/admin/package/update/1');

        $package = \Robinson\Backend\Models\Package::findFirst();
        $this->assertEquals(2, $package->getDestination()->getDestinationId());
        $this->assertEquals('test package name 2 :)', $package->getPackage());
        $this->assertEquals('test package description 2 :)', $package->getDescription());
        $this->assertEquals(999, $package->getPrice());
        $this->assertEquals(\Robinson\Backend\Models\Package::STATUS_VISIBLE, $package->getStatus());
        $this->assertEquals('packagepdftest.pdf', $package->getPdf());

        // assert tabs
        $this->assertGreaterThan(0, $package->getTabs()->count());

        foreach ($package->getTabs() as $tab)
        {
            foreach ($_POST['tabs'] as $type => $description)
            {
                if($tab->getType() === $type)
                {
                    $this->assertEquals($description, $tab->getDescription());
                }
            }
        }
    }
    
    public function testUpdatePackageWithNewPdfAndImageAndTabShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 999,
            'status' => 1,
            'tabs' => array
            (
                1 => 'tab',
            ),
        );
        
        $mockImageFile = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'moveTo'))
            ->getMock();
        $mockImageFile->expects($this->exactly(3))
            ->method('getName')
            ->will($this->returnValue('packageimagetest.jpg'));
        $mockImageFile->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(true));
        
        $mockPdfFile = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'moveTo', 'getKey'))
            ->getMock();
        $mockPdfFile->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('packagepdftest.pdf'));
        $mockPdfFile->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(true));
        $mockPdfFile->expects($this->once())
            ->method('getKey')
            ->will($this->returnValue('pdf'));
        
        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('getUploadedFiles'))
            ->getMock();
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $mockPdfFile,
                1 => $mockImageFile,
            )));
        $this->getDI()->set('request', $request, true);

        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        
        $packageImage = $this->getMockBuilder('Robinson\Backend\Models\Images\Package')
            ->setMethods(array('applyWatermark'))
            ->getMock();
        $packageImage->expects($this->any())
            ->method('applyWatermark')
            ->will($this->returnValue(true));
        $this->getDI()->set('Robinson\Backend\Models\Images\Package', $packageImage);
        
        $this->dispatch('/admin/package/update/1');
        
        $package = \Robinson\Backend\Models\Package::findFirst();
        $this->assertEquals(2, $package->getDestination()->getDestinationId());
        $this->assertEquals('test package name 2 :)', $package->getPackage());
        $this->assertEquals('test package description 2 :)', $package->getDescription());
        $this->assertEquals(999, $package->getPrice());
        $this->assertEquals(\Robinson\Backend\Models\Package::STATUS_VISIBLE, $package->getStatus());
        // pdf set?
        $this->assertEquals('packagepdftest.pdf', $package->getPdf());
        // image set?
        $image = \Robinson\Backend\Models\Images\Package::findFirst(6);
        $this->assertEquals('6-packageimagetest.jpg', $image->getRealFileName());
        $this->assertEquals('packageimagetest.jpg', $image->getTitle());
        
        // assert tabs
        $this->assertGreaterThan(0, $package->getTabs()->count());
        
        foreach ($package->getTabs() as $tab)
        {
             foreach ($_POST['tabs'] as $type => $description)
             {
                 if($tab->getType() === $type)
                 {
                     $this->assertEquals($description, $tab->getDescription());
                 }
             }
         }
    }

    public function testSortingPackageImagesAndAddingNewTabShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 999,
            'status' => 1,
            'sort' => array
            (
                1 => 2,
                2 => 3,
                3 => 4,
                4 => 5,
                5 => 6,
            ),
            'tabs' => array
            (
                1 => 'tab1',
            ),
        );


        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        $this->dispatch('/admin/package/update/1');
        
        $package = \Robinson\Backend\Models\Package::findFirst();
        $this->assertEquals(2, $package->getDestination()->getDestinationId());
        $this->assertEquals('test package name 2 :)', $package->getPackage());
        $this->assertEquals('test package description 2 :)', $package->getDescription());
        $this->assertEquals(999, $package->getPrice());
        $this->assertEquals(\Robinson\Backend\Models\Package::STATUS_VISIBLE, $package->getStatus());
        $images = \Robinson\Backend\Models\Images\Package::find(array
        (
            'packageId' => 1,
        ));
        
        foreach ($images as $image)
        {
            $this->assertEquals($_POST['sort'][$image->getImageId()], $image->getSort());
        }
        
        // assert tabs
        $this->assertGreaterThan(0, $package->getTabs()->count());
        
        foreach ($package->getTabs() as $tab)
        {
             foreach ($_POST['tabs'] as $type => $description)
             {
                 if($tab->getType() === $type)
                 {
                     $this->assertEquals($description, $tab->getDescription());
                 }
             }
         }
    }
    
    public function testChangingTitlesOnImagesAndAddingTabsShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 999,
            'status' => 1,
            'title' => array
            (
                1 => 'image 1',
                2 => 'image 2',
                3 => 'image 3',
                4 => 'image 4',
                5 => 'image 5',
            ),
            'tabs' => array
            (
                1 => 'aaa1',
                2 => '',
            ),
        );

        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        
        $this->dispatch('/admin/package/update/1');
        
        $package = \Robinson\Backend\Models\Package::findFirst();
        $this->assertEquals(2, $package->getDestination()->getDestinationId());
        $this->assertEquals('test package name 2 :)', $package->getPackage());
        $this->assertEquals('test package description 2 :)', $package->getDescription());
        $this->assertEquals(999, $package->getPrice());
        $this->assertEquals(\Robinson\Backend\Models\Package::STATUS_VISIBLE, $package->getStatus());
        $images = \Robinson\Backend\Models\Images\Package::find(array
        (
            'packageId' => 1,
        ));
        
        foreach ($images as $image)
        {
            $this->assertEquals($_POST['title'][$image->getImageId()], $image->getTitle());
        }
        
        // assert tabs
        $this->assertGreaterThan(0, $package->getTabs()->count());
        
        foreach ($package->getTabs() as $tab)
        {
             foreach ($_POST['tabs'] as $type => $description)
             {
                 if($tab->getType() === $type)
                 {
                     $this->assertEquals($description, $tab->getDescription());
                 }
             }
         }
    }
    
    public function testChangingTitlesOnImagesWithNewImageUploadShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 999,
            'status' => 1,
            'title' => array
            (
                1 => 'image 1',
                2 => 'image 2',
                3 => 'image 3',
                4 => 'image 4',
                5 => 'image 5',
            ),
            'tabs' => array
            (
                1 => 'newtab',
            ),
        );
        
        
        // new image
        $mockImageFile = $this->getMockBuilder('Phalcon\Http\Request\File')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'moveTo'))
            ->getMock();
        $mockImageFile->expects($this->exactly(3))
            ->method('getName')
            ->will($this->returnValue('newpngimage.png'));
        $mockImageFile->expects($this->once())
            ->method('moveTo')
            ->will($this->returnValue(true));
       
        // request
        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('getUploadedFiles'))
            ->getMock();
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $mockImageFile,
            )));
        $this->getDI()->set('request', $request, true);

        $this->getDI()->set('Imagick', $this->mockWorkingImagick());
        
        $packageImage = $this->getMockBuilder('Robinson\Backend\Models\Images\Package')
            ->setMethods(array('applyWatermark'))
            ->getMock();
        $packageImage->expects($this->any())
            ->method('applyWatermark')
            ->will($this->returnValue(true));
        $this->getDI()->set('Robinson\Backend\Models\Images\Package', $packageImage);
        
        $this->dispatch('/admin/package/update/1');
        
        $package = \Robinson\Backend\Models\Package::findFirst();
        $this->assertEquals(2, $package->getDestination()->getDestinationId());
        $this->assertEquals('test package name 2 :)', $package->getPackage());
        $this->assertEquals('test package description 2 :)', $package->getDescription());
        $this->assertEquals(999, $package->getPrice());
        $this->assertEquals(\Robinson\Backend\Models\Package::STATUS_VISIBLE, $package->getStatus());
        $images = \Robinson\Backend\Models\Images\Package::find(array
        (
            'packageId' => 1,
        ));
        
        foreach ($images as $image)
        {
            // new image, do not check changed title
            if($image->getImageId() === 6)
            {
                continue;
            }
            $this->assertEquals($_POST['title'][$image->getImageId()], $image->getTitle());
        }

        // image set?
        $image = \Robinson\Backend\Models\Images\Package::findFirst(6);
        $this->assertEquals('6-newpngimage.png', $image->getRealFileName());
        $this->assertEquals('newpngimage.png', $image->getTitle());
        
        // assert tabs
        $this->assertGreaterThan(0, $package->getTabs()->count());
        
        foreach ($package->getTabs() as $tab)
        {
             foreach ($_POST['tabs'] as $type => $description)
             {
                 if($tab->getType() === $type)
                 {
                     $this->assertEquals($description, $tab->getDescription());
                 }
             }
         }
    }
    
    public function testUpdatingExistingTabsShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 1,
            'package' => 'test package name 4 :)',
            'description' => 'test package description 4 :)',
            'price' => 999,
            'status' => 1,
            'tabs' => array
            (
                1 => 'newtab',
                2 => '',
                3 => '',
            ),
        );

        $this->dispatch('/admin/package/update/4');
        
        $package = \Robinson\Backend\Models\Package::findFirst(4);
        $this->assertEquals(1, $package->getDestination()->getDestinationId());
        $this->assertEquals('test package name 4 :)', $package->getPackage());
        $this->assertEquals('test package description 4 :)', $package->getDescription());
        $this->assertEquals(999, $package->getPrice());
        $this->assertEquals(\Robinson\Backend\Models\Package::STATUS_VISIBLE, $package->getStatus());
    
        // assert tabs
        $this->assertGreaterThan(0, $package->getTabs()->count());
        
        foreach ($package->getTabs() as $tab)
        {
             foreach ($_POST['tabs'] as $type => $description)
             {
                 if($tab->getType() === $type)
                 {
                     $this->assertEquals($description, $tab->getDescription());
                 }
             }
         }
         
         // two were deleted
         $this->assertCount(1, $package->getTabs());
    }
    
    public function testDeleteImageShouldExist()
    {
        $this->registerMockSession();
        $this->dispatch('/admin/package/deleteImage/1');
        $this->assertAction('deleteImage');
        $this->assertController('package');
    }
    
    public function testPdfPreviewActionShouldWorkAsExpected()
    {
        $this->registerMockSession();
        \org\bovigo\vfs\vfsStream::create(array
        (
            'pdf' => array
            (
                'package' => array
                (
                    1 => array
                    (
                        'pdffile-1.pdf.html' => '<html><head><title>title</title></head><body></body></html>',
                    ),
                )
            ),
        ));
        $this->dispatch('/admin/package/pdfPreview/1');
        $this->assertEquals('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><head><base href="/pdf/package/1/"></head><body></body></html>', trim($this->getContent()));
    }

    public function testUnsettingTagShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array
        (
            'destinationId' => 1,
            'package' => 'test package name 4 :)',
            'description' => 'test package description 4 :)',
            'price' => 999,
            'status' => 1,
            'tabs' => array
            (
                1 => 'newtab',
                2 => '',
                3 => '',
            ),
            'tags' => array
            (
                2 => 'Last minute',
            ),
        );

        $this->dispatch('/admin/package/update/3');
        $package = \Robinson\Backend\Models\Package::findFirst(3);
        $this->assertCount(1, $package->getTags());
    }
}
