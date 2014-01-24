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
        $this->view->destinations = array();
        
        if ($this->request->hasQuery('categoryId'))
        {
            $destinations = $this->getDI()->get('Robinson\Backend\Models\Destination');
            $this->view->destinations = $destinations->find(array
            (
                'categoryId' => $this->request->getQuery('categoryId'),
                'order' => 'destinationId DESC',
            ));
            $this->tag->setDefault('categoryId', $this->request->getQuery('categoryId'));
        }
        
        $categories = \Robinson\Backend\Models\Category::find(array
        (
            'order' => 'categoryId DESC',
        ));
        
        $this->view->categories = $categories;
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
            /* @var $destination \Robinson\Backend\Models\Destination */
            $destination = $this->getDI()->get('Robinson\Backend\Models\Destination');
            $destination->setCategoryId($this->request->getPost('categoryId'))
                ->setDestination($this->request->getPost('destination'))
                ->setDescription($this->request->getPost('description'))
                ->setStatus($this->request->getPost('status'));
            
            $destinationTabs = array();
            foreach ($this->request->getPost('tabs') as $tabType => $tabDescription)
            {
                $destinationTab = new \Robinson\Backend\Models\Tabs\Destination();
                $destinationTab->setType($tabType)
                    ->setTitle($destinationTab->resolveTypeToTitle())
                    ->setDescription($tabDescription);
                $destinationTabs[] = $destinationTab;
            }
            
            $destination->setTabs($destinationTabs);
            
            // redirect to update upon successful save
            if ($destination->create())
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
    
    /**
     * Updates destination by id. Accepts images.
     * 
     * @return void
     */
    public function updateAction()
    {
        /* @var $destination \Robinson\Backend\Models\Destination */
        $destination = \Robinson\Backend\Models\Destination::findFirstByDestinationId($this->dispatcher
            ->getParam('id'));

        if ($this->request->isPost())
        {
            $destination->setCategoryId($this->request->getPost('categoryId'))
                ->setDestination($this->request->getPost('destination'))
                ->setDescription($this->request->getPost('description'))
                ->setStatus($this->request->getPost('status'));
            
            $destinationTabs = array();
            
            foreach ($this->request->getPost('tabs') as $tabType => $tabDescription)
            {
                $tab = $destination->getTabs(array
                (
                    'type = :type:',
                    'bind' => array
                    (
                        'type' => $tabType,
                    ),
                ))->getFirst();
                
                if (!$tabDescription)
                {
                    $tab->delete();
                    continue;
                }
                
                $tab->setDescription($tabDescription);
                $destinationTabs[] = $tab;
            }
            
            $destination->setTabs($destinationTabs);
            
            // sort?
            $sort = $this->request->getPost('sort');
            
            if ($sort)
            {
                $images = array();
                // bug here ? if loop thru $destination->images, then cannot save related images on next update
                foreach ($destination->getImages() as $image)
                {
                    $image->setSort($sort[$image->getImageId()]);
                    $image->update();
                }
            }
            
            $images = array();
            
            $files = $this->request->getUploadedFiles();
            foreach ($files as $file)
            {
                /* @var $imageCategory \Robinson\Backend\Models\Images\Destination */
                $destinationImage = $this->getDI()->get('Robinson\Backend\Models\Images\Destination');
                $destinationImage->createFromUploadedFile($file);
                $images[] = $destinationImage;
            }
         
            $destination->images = $images;
            
            $destination->update();
            
        }
        
        $categories = \Robinson\Backend\Models\Category::find(array
        (
            'order' => 'categoryId DESC',
        ));
        
        $this->view->categories = $categories;
        $this->view->destination = $destination;
        $this->tag->setDefault('categoryId', $destination->getCategoryId());
        $this->tag->setDefault('destination', $destination->getDestination());
        $this->tag->setDefault('description', $destination->getDescription());
        
    }
    
    /**
     * Deletes destination image. Outputs JSON.
     * 
     * @return string json response
     */
    public function deleteImageAction()
    {
        $image = \Robinson\Backend\Models\Images\Destination::findFirst($this->request->getPost('id'));
        $this->response->setJsonContent(array('response' => $image->delete()))->setContentType('application/json');
        return $this->response;
    }
}