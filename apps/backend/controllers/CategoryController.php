<?php
namespace Robinson\Backend\Controllers;
class CategoryController extends \Robinson\Backend\Controllers\ControllerBase
{
    public function createAction()
    {
        if($this->request->isPost())
        {
            $category = new \Robinson\Backend\Models\Category();
            $category->setCategory($this->request->getPost('category'))
                ->setDescription($this->request->getPost('description'))
                ->setStatus($this->request->getPost('status'))
                ->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Belgrade')))
                ->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Belgrade')))
                ->save();
            $this->flash->success('Kategorija snimljena');
            return $this->response->redirect(array('for' => 'admin', 'controller' => 'index', 'action' => 'dashboard'));
        }
    }
    
    public function updateAction()
    {
        /* @var $category \Robinson\Backend\Models\Category */
        $category = $this->getDI()->get('Robinson\Backend\Models\Category');
        $category = $category->findFirst('categoryId = ' . $this->dispatcher->getParam('id'));      

        if($this->request->isPost())
        {
            
            $category->setCategory($this->request->getPost('category'))
                ->setDescription($this->request->getPost('description'))
                ->setStatus($this->request->getPost('status'))
                ->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Belgrade')));
     
            // files upload
            $files = $this->request->getUploadedFiles();

            foreach($files as $file)
            {
                $imageCategory = \Robinson\Backend\Models\ImageCategory::createFromUploadedFile($file);
                $imageCategory->setCategoryId($category->getCategoryId());
                $imageCategory->save();
                array_push($category->getImages(), $imageCategory);
            }
            
            // sort
            foreach($category->getImages() as $image)
            {
                $sort = $this->request->getPost('sort')[$image->getImageCategoryId()];
                if($sort)
                {
                    $image->setSort($this->request->getPost('sort')[$image->getImageCategoryId()])->update();
                }
            }

            $category->update();
            $category->refresh();
        }
        
        $this->tag->setDefault('status', $category->getStatus());
        $this->tag->setDefault('description', $category->getDescription());
        
        $this->view->setVar('category', $category);
    }
    
    public function deleteImageAction()
    {
        $this->view->disable();
        /* @var $images \Robinson\Backend\Models\ImageCategory */ 
        $images = $this->getDI()->get('Robinson\Backend\Models\ImageCategory');
        /* @var $image \Robinson\Backend\Models\ImageCategory */
        $image = $images->findFirst($this->request->getPost('id'));
        $this->response->setJsonContent(array('response' => $image->delete()))->setContentType('application/json');
        return $this->response;
    }
}