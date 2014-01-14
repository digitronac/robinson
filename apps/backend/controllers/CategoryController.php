<?php
namespace Robinson\Backend\Controllers;
class CategoryController extends \Robinson\Backend\Controllers\ControllerBase
{
    /**
     * Page where list of categories is displayed.
     * 
     * @return void
     */
    public function indexAction()
    {
        /* @var $categories \Phalcon\Mvc\Model\Resultset\Simple */
        $categories = \Robinson\Backend\Models\Category::find(array('order' => 'categoryId DESC'));
        $this->view->setVar('categories', $categories);
    }
    
    /**
     * Create new category page.
     * 
     * @return mixed
     */
    public function createAction()
    {
        $isPost = $this->request->isPost();

        if ($this->request->isPost())
        {
            $category = new \Robinson\Backend\Models\Category();
            $category
                ->setCategory($this->request->getPost('category'))
                ->setDescription($this->request->getPost('description'))
                ->setStatus($this->request->getPost('status'));
            $category
                ->setCreatedAt(new \DateTime('now', new \DateTimeZone(date_default_timezone_get())))
                ->setUpdatedAt(new \DateTime('now', new \DateTimeZone(date_default_timezone_get())))
                ->save();

            return $this->response->redirect(array('for' => 'admin-update', 'controller' => 'category', 
                'action' => 'update', 'id' => $category->getCategoryId()));
        }
    }
    
    /**
     * Update existing category.
     * 
     * @return void
     */
    public function updateAction()
    {
        /* @var $category \Robinson\Backend\Models\Category */
        $category = $this->getDI()->get('Robinson\Backend\Models\Category');
        $category = $category->findFirst('categoryId = ' . $this->dispatcher->getParam('id'));   

        if ($this->request->isPost())
        {
            $category->setCategory($this->request->getPost('category'))
                ->setDescription($this->request->getPost('description'))
                ->setStatus($this->request->getPost('status'))
                ->setUpdatedAt(new \DateTime('now', new \DateTimeZone(date_default_timezone_get())));
     
            $images = array();
            // files upload
            $files = $this->request->getUploadedFiles();

            foreach ($files as $file)
            {
                /* @var $imageCategory \Robinson\Backend\Models\Images\Category */
                $imageCategory = $this->getDI()->get('Robinson\Backend\Models\Images\Category');
                $imageCategory->createFromUploadedFile($file);
                $images[] = $imageCategory;
            }

            // sort
            foreach ($category->getImages() as $image)
            {
                $sort = $this->request->getPost('sort')[$image->getImageId()];
                if ($sort)
                {
                    $image->setSort($this->request->getPost('sort')[$image->getImageId()])->update();
                }
            }
            
            $category->images = $images;
            $category->update();
            $category->refresh();
        }
        
        $this->tag->setDefault('status', $category->getStatus());
        $this->tag->setDefault('description', $category->getDescription());
        
        $this->view->setVar('category', $category);
    }
    
    /**
     * Delete category related image, returns json object.
     *  
     * @return \Phalcon\Http\Response
     */
    public function deleteImageAction()
    {
        $this->view->disable();
        /* @var $images \Robinson\Backend\Models\Images\Category */ 
        $images = $this->getDI()->get('Robinson\Backend\Models\Images\Category');
        /* @var $image \Robinson\Backend\Models\Images\Category */
        $image = $images->findFirst($this->request->getPost('id'));
        
        $this->response->setJsonContent(array('response' => $image->delete()))->setContentType('application/json');
        return $this->response;
    }
}