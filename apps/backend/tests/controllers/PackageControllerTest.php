<?php
namespace Robinson\Backend\Tests\Controllers;
// @codingStandardsIgnoreStart
class PackageControllerTest extends \Robinson\Backend\Tests\Controllers\BaseTestController
{
    protected $vfsfs;
    
    protected function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('packages');
        $this->populateTable('package_images');
        $this->vfsfs = \org\bovigo\vfs\vfsStream::setup('root', null, array
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
        
        $this->getDI()->getShared('config')->application->packagePdfPath = \org\bovigo\vfs\vfsStream::url('root/pdf/package');
        $this->getDI()->getShared('config')->application->packageImagesPath = \org\bovigo\vfs\vfsStream::url('root/img/package');
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
        
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        
        $this->getDI()->set('Imagick', $mockImagick);
        $this->dispatch('/admin/package/update/1');
        $this->assertAction('update');
        $this->assertController('package');
    }
    
    public function testUpdatePackageWithNewImageShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 999,
            'status' => 1,
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
            ->setMethods(array('isPost', 'getUploadedFiles'))
            ->getMock();
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $mockImageFile,
            )));
        
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        $this->getDI()->set('Imagick', $mockImagick);
        
        $this->getDI()->setShared('request', $request);
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
    }
    
    public function testUpdatePackageWithNewPdfShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 999,
            'status' => 1,
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
        
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        $this->getDI()->set('Imagick', $mockImagick);
        
        $this->getDI()->setShared('request', $request);
        $this->dispatch('/admin/package/update/1');
        
        $package = \Robinson\Backend\Models\Package::findFirst();
        $this->assertEquals(2, $package->getDestination()->getDestinationId());
        $this->assertEquals('test package name 2 :)', $package->getPackage());
        $this->assertEquals('test package description 2 :)', $package->getDescription());
        $this->assertEquals(999, $package->getPrice());
        $this->assertEquals(\Robinson\Backend\Models\Package::STATUS_VISIBLE, $package->getStatus());
        $this->assertEquals('packagepdftest.pdf', $package->getPdf());
    }
    
    public function testUpdatePackageWithNewPdfAndImageShouldWorkAsExpected()
    {
        $this->registerMockSession();
        $_POST = array
        (
            'destinationId' => 2,
            'package' => 'test package name 2 :)',
            'description' => 'test package description 2 :)',
            'price' => 999,
            'status' => 1,
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
                1 => $mockImageFile,
            )));
        
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        $this->getDI()->set('Imagick', $mockImagick);
        
        $this->getDI()->setShared('request', $request);
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
    }
    
    public function testSortingPackageImagesShouldWorkAsExpected()
    {
        $this->registerMockSession();
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
        );
        
       
        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('isPost'))
            ->getMock();
        $request->expects($this->any())
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
    }
    
    public function testChangingTitlesOnImagesShouldWorkAsExpected()
    {
        $this->registerMockSession();
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
        );
        
       
        $request = $this->getMockBuilder('Phalcon\Http\Request')
            ->setMethods(array('isPost'))
            ->getMock();
        $request->expects($this->any())
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
    }
    
    public function testChangingTitlesOnImagesWithNewImageUploadShouldWorkAsExpected()
    {
        $this->registerMockSession();
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
            ->setMethods(array('isPost', 'getUploadedFiles'))
            ->getMock();
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $mockImageFile,
            )));
        
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->any())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->any())
            ->method('writeimage')
            ->will($this->returnValue(true));
        $this->getDI()->set('Imagick', $mockImagick);
        
        $this->getDI()->setShared('request', $request);
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
        ), $this->vfsfs);
        $this->dispatch('/admin/package/pdfPreview/1');
        $this->assertEquals('<html><head><base href="/pdf/package/1/"></head><body></body></html>', trim($this->getContent()));
    }
}
