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
        $this->view->select = $this->buildDestinationMultiSelectData();
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
    
    /**
     * Builds data to be used in multi select form element.
     * 
     * @return array
     */
    protected function buildDestinationMultiSelectData()
    {
        $categories = \Robinson\Backend\Models\Category::find(array
        (
            'order' => 'category',
        ));
        
        // build select
        $select = array();
        foreach ($categories as $category)
        {
            $select[$category->getCategory()] = array();
            
            foreach ($category->getDestinations() as $destination)
            {
                $select[$category->getCategory()][$destination->getDestinationId()] = $destination->getDestination();
            }
        }
        
        return $select;
    }
}