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
    
    /**
     * Updates destination by id. Accepts images.
     * 
     * @return void
     */
    public function updateAction()
    {
        /* @var $destination \Robinson\Backend\Models\Destinations */
        $destination = \Robinson\Backend\Models\Destinations::findFirstByDestinationId($this->dispatcher
            ->getParam('id'));

        if ($this->request->isPost())
        {
            $destination->setCategoryId($this->request->getPost('categoryId'))
                ->setDestination($this->request->getPost('destination'))
                ->setDescription($this->request->getPost('description'))
                ->setStatus($this->request->getPost('status'))
                ->setUpdatedAt(new \DateTime('now', new \DateTimeZone(date_default_timezone_get())));
            
            // sort?
            $sort = $this->request->getPost('sort');
            
            if ($sort)
            {
                $images = array();
                // bug here ? if loop thru $destination->images, then cannot save related images on next update
                foreach ($destination->getImages() as $image)
                {
                    $image->setSort($sort[$image->getDestinationImageId()]);
                    $image->update();
                }
            }
            
            $images = array();
            
            $files = $this->request->getUploadedFiles();
            foreach ($files as $file)
            {
                /* @var $imageCategory \Robinson\Backend\Models\DestinationImages */
                $destinationImage = $this->getDI()->get('Robinson\Backend\Models\DestinationImages');
                $destinationImage->createFromUploadedFile($file, $destination->getDestinationId());
                $images[] = $destinationImage;
            }
         
            $destination->images = $images;
            
            $destination->save();
            
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
        $image = \Robinson\Backend\Models\DestinationImages::findFirst($this->request->getPost('id'));
        $this->response->setJsonContent(array('response' => $image->delete()))->setContentType('application/json');
        return $this->response;
    }
}