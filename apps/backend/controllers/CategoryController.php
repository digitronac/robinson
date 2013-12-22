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
        
    }
}