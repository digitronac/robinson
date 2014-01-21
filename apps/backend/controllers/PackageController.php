<?php
namespace Robinson\Backend\Controllers;
class PackageController extends \Robinson\Backend\Controllers\ControllerBase
{
    /**
     * Creates new package.
     * 
     * @return void
     */
    public function createAction()
    {
        // create pdf
        if ($this->request->isPost())
        {
            $destination = $this->getDI()->get('Robinson\Backend\Models\Destinations');
            $destination = $destination->findFirst($this->request->getPost('destinationId'));
            /* @var $package \Robinson\Backend\Models\Package */
            $package = $this->getDI()->get('Robinson\Backend\Models\Package');
            $package->setPackage($this->request->getPost('package'))
                ->setDestination($destination)
                ->setTabs('aaaa')
                ->setPrice($this->request->getPost('price'))
                ->setDescription($this->request->getPost('description'))
                ->setUploadedPdf($this->request->getUploadedFiles()[0])
                ->setStatus($this->request->getPost('status'));
            if (!$package->create())
            {
                throw new \Phalcon\Exception('Unable to create new package.');
            }
            
            return $this->response->redirect(array
            (
                'for' => 'admin-update',
                'controller' => 'package',
                'action' => 'update',
                'id' => $package->getPackageId(),
            ));
        }

        $this->view->select = $this->buildDestinationMultiSelectData();
    }
    
    /**
     * Updates existing package.
     * 
     * @return void
     */
    public function updateAction()
    {
        /* @var $package \Robinson\Backend\Models\Package */
        $package = $this->getDI()->get('Robinson\Backend\Models\Package');
        /* @var $package \Robinson\Backend\Models\Package */
        $package = $package->findFirst($this->dispatcher->getParam('id'));
        
        if ($this->request->isPost())
        {
            $destination = $this->getDI()->get('Robinson\Backend\Models\Destinations');
            $destination = $destination->findFirst($this->request->getPost('destinationId'));
            $package->setPackage($this->request->getPost('package'))
                ->setDestination($destination)
                ->setPrice($this->request->getPost('price'))
                ->setDescription($this->request->getPost('description'))
                ->setStatus($this->request->getPost('status'));
            
            $images = array();
            $files = $this->request->getUploadedFiles();
            foreach ($files as $file)
            {
                if ($file->getKey() === 'pdf')
                { 
                    $package->setUploadedPdf($file);
                    continue;
                }
                
                /* @var $packageImage \Robinson\Backend\Models\Images\Package */
                $packageImage = $this->getDI()->get('Robinson\Backend\Models\Images\Package');
                $packageImage->createFromUploadedFile($file)
                    ->setTitle('package');

               $images[] = $packageImage;
            }
            
            if ($images)
            {
                $package->images = $images;
            }
            
            if (!$package->update())
            {
                throw new \Phalcon\Exception('Unable to update package #' . $package->getPackageId());
            }
        }
        
        $this->tag->setDefault('destinationId', $package->getDestination()->getDestination());
        $this->tag->setDefault('package', $package->getPackage());
        $this->tag->setDefault('price', $package->getPrice());
        $this->tag->setDefault('description', $package->getDescription());
        $this->tag->setDefault('status', $package->getStatus());
        $this->view->defaultDestinationId = $package->getDestination()->getDestinationId();
        
        $this->view->select = $this->buildDestinationMultiSelectData();
        $this->view->package = $package;
    }
    
    /**
     * Displays package PDF preview.
     * 
     * @return void
     */
    public function pdfPreviewAction()
    {
        $this->view->disable();
        $package = $this->getDI()->get('Robinson\Backend\Models\Package');
        /* @var $package \Robinson\Backend\Models\Package */
        $package = $package->findFirst($this->dispatcher->getParam('id'));
        
        /* @var $pdf \Robinson\Backend\Models\Pdf */
        $pdf = $this->getDI()->get('Robinson\Backend\Models\Pdf', array
        (
            $this->fs, $package, $this->config->application->packagePdfPath,
        ));
        
        echo $pdf->getHtmlDocument($this->config->application->packagePdfWebPath)->saveHTML();
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