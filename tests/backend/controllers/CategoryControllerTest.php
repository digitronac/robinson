<?php
namespace Robinson\Backend\Controllers;
class CategoryControllerTest extends BaseTestController
{
    public function setUp(\Phalcon\DiInterface $di = null, \Phalcon\Config $config = null)
    {
        parent::setUp($di, $config);
        $this->populateTable('Category');
    }
    public function testCategoryCreateShouldExist()
    {
        // logged in
        $this->registerMockSession();
        $this->dispatch('/admin/category/create');
        $this->assertResponseContentContains('<textarea name="description" class="ckeditor" placeholder="Tekst" required="required"></textarea>');
    }
    
    public function testCategoryCreateSubmitShouldCreateNewRecord()
    {
        // logged in
        $this->registerMockSession();
        $category = 'test category';
        $description = 'this is some category description';
        $status = \Robinson\Backend\Models\Category::STATUS_INVISIBLE;
        
        $request = $this->getMock('Phalcon\Http\Request', array('getPost', 'isPost'));
   
        $request->expects($this->at(2))
            ->method('getPost')
            ->with($this->equalTo('category'))
            ->will($this->returnValue($category));
        
        $request->expects($this->at(3))
            ->method('getPost')
            ->with($this->equalTo('description'))
            ->will($this->returnValue($description));
        
        $request->expects($this->at(4))
            ->method('getPost')
            ->with($this->equalTo('status'))
            ->will($this->returnValue($status));
 
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));
        
        $this->getDI()->setShared('request', $request);
      
        $this->dispatch('/admin/category/create');
        $this->assertRedirectTo('/admin/index/dashboard');
        /* @var $categoryModel \Robinson\Backend\Models\Category */
        $categoryModel = \Robinson\Backend\Models\Category::findFirst("category = '$category'");
        $this->assertEquals($category, $categoryModel->getCategory());
        $this->assertEquals($description, $categoryModel->getDescription());
        $this->assertEquals($status, $categoryModel->getStatus());
        $this->assertNotEmpty($categoryModel->getCreatedAt());
        $this->assertNotEmpty($categoryModel->getUpdatedAt());
    }
    
    public function testCategoryUpdateShouldWorkAsExpected()
    {
        $this->registerMockSession();
        /* @var $category \Robinson\Backend\Models\Category */
        $category = \Robinson\Backend\Models\Category::findFirst();
        
        $request = $this->getMock('Phalcon\Http\Request', array('getPost', 'isPost'));
   
        $request->expects($this->at(1))
            ->method('getPost')
            ->with($this->equalTo('category'))
            ->will($this->returnValue($category->getCategory() . ' updated!'));
        
        $request->expects($this->at(2))
            ->method('getPost')
            ->with($this->equalTo('description'))
            ->will($this->returnValue($category->getDescription() . ' updated!'));
        
        $request->expects($this->at(3))
            ->method('getPost')
            ->with($this->equalTo('status'))
            ->will($this->returnValue($category->getStatus(\Robinson\Backend\Models\Category::STATUS_INVISIBLE)));
 
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));
        
        $this->getDI()->setShared('request', $request);
        $this->dispatch('/admin/category/update/' . $category->getCategoryId());
        $updatedCategory = \Robinson\Backend\Models\Category::findFirst("category = '" . $category->getCategory() . " updated!'");
        
        $this->assertEquals($category->getCategory() . ' updated!', $updatedCategory->getCategory());
        $this->assertEquals($category->getDescription() . ' updated!', $updatedCategory->getDescription());
        $this->assertEquals($category->getDescription() . ' updated!', $updatedCategory->getDescription());
        $this->assertEquals(\Robinson\Backend\Models\Category::STATUS_VISIBLE, $updatedCategory->getStatus());
    }
    
    public function testCategoryUpdateWithAddedImageShouldWorkAsExpected()
    {
        $this->registerMockSession();
        /* @var $category \Robinson\Backend\Models\Category */
        $category = \Robinson\Backend\Models\Category::findFirst();
        
        $request = $this->getMock('Phalcon\Http\Request', array('getPost', 'isPost', 'getUploadedFiles'));
   
        $request->expects($this->at(1))
            ->method('getPost')
            ->with($this->equalTo('category'))
            ->will($this->returnValue($category->getCategory() . ' updated!'));
        
        $request->expects($this->at(2))
            ->method('getPost')
            ->with($this->equalTo('description'))
            ->will($this->returnValue($category->getDescription() . ' updated!'));
        
        $request->expects($this->at(3))
            ->method('getPost')
            ->with($this->equalTo('status'))
            ->will($this->returnValue($category->getStatus(\Robinson\Backend\Models\Category::STATUS_INVISIBLE)));
 
        $request->expects($this->any())
            ->method('isPost')
            ->will($this->returnValue(true));
        
        //$file = new \Phalcon\Http\Request\File('testfile.png');
        include APPLICATION_PATH . '/../tests/stubs/File.php';
        $fileStub = new \Robinson\Stub\Request\File('aaa');
        $request->expects($this->once())
            ->method('getUploadedFiles')
            ->will($this->returnValue(array
            (
                0 => $fileStub,
            )));
            
        $mockImagick = $this->getMock('Imagick', array('scaleimage', 'writeimage'));
        $mockImagick->expects($this->once())
            ->method('scaleimage')
            ->will($this->returnValue(true));
        $mockImagick->expects($this->once())
            ->method('writeimage')
            ->will($this->returnValue(true));
        
        $this->getDI()->setShared('request', $request);
        $this->getDI()->set('Imagick', $mockImagick);
        $this->dispatch('/admin/category/update/' . $category->getCategoryId());
        
        $this->assertResponseContentContains('<dt>Slike:</dt>
            
                        
            <dd>
                <img');
        
        $updatedCategory = \Robinson\Backend\Models\Category::findFirst("category = '" . $category->getCategory() . " updated!'");
        
        $this->assertEquals($category->getCategory() . ' updated!', $updatedCategory->getCategory());
        $this->assertEquals($category->getDescription() . ' updated!', $updatedCategory->getDescription());
        $this->assertEquals($category->getDescription() . ' updated!', $updatedCategory->getDescription());
        $this->assertEquals(\Robinson\Backend\Models\Category::STATUS_VISIBLE, $updatedCategory->getStatus());
    }
    
    public function tearDown()
    {
        $this->truncateTable('ImageCategory');
        $this->truncateTable('Category');
        parent::tearDown();
    }
}