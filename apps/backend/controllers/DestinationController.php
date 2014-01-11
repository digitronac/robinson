<?php
namespace Robinson\Backend\Controllers;
class DestinationController extends \Phalcon\Mvc\Controller
{
    /**
     * Page where detailed list of destinations is displayed.
     * 
     * @return void
     */
    public function indexAction()
    {
        $categories = \Robinson\Backend\Models\Category::find(array
        (
            'order' => 'categoryId DESC',
        ));
        
        $this->view->setVar('categories', $categories);
    }
    
    /**
     * Creates new destination. If successful will redirect to update page of that destination.
     * 
     * @return void
     */
    public function createAction()
    {
        if ($this->request->isPost())
        {
            /* @var $destination \Robinson\Backend\Models\Destinations */
            $destination = $this->getDI()->get('Robinson\Backend\Models\Destinations');
            $destination->setCategoryId($this->request->getPost('categoryId'))
                ->setDestination($this->request->getPost('destination'))
                ->setDescription($this->request->getPost('description'))
                ->setStatus($this->request->getPost('status'))
                ->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Belgrade')))
                ->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Belgrade')));
            
            // redirect to update upon successful save
            if ($destination->save())
            {
                return $this->response->redirect(array
                (
                    'for' => 'admin-update',
                    'controller' => 'destination',
                    'action' => 'update',
                    'id' => $destination->getDestinationId(),
                ));
            }
        }
        
        $categories = \Robinson\Backend\Models\Category::find(array
        (
            'order' => 'categoryId DESC',
        ));
        
        $this->view->setVar('categories', $categories);
    }
}