<?php
namespace Robinson\Backend\Controllers;

class PackageController extends \Robinson\Backend\Controllers\ControllerBase
{
    /**
     * Index page, lists packages.
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->view->packages = array();

        if ($this->session->get('destinationId') && !$this->request->hasQuery('destinationId')) {
            $this->tag->setDefault('destinationId', (int) $this->session->get('destinationId'));
            $_GET['destinationId'] = (int) $this->session->get('destinationId');
            $this->session->remove('destinationId');
        }

        if ($this->request->hasQuery('destinationId')) {
            $packages = $this->getDI()->get('Robinson\Backend\Models\Package');
            $this->view->packages = $packages->find(
                array(
                    'destinationId = ' . $this->request->getQuery('destinationId'),
                    'order' => 'destinationId DESC',
                )
            );

            $this->session->set('destinationId', (int) $this->request->getQuery('destinationId'));
            $this->tag->setDefault('destinationId', (int) $this->request->getQuery('destinationId'));
        }
        
        $this->view->select = $this->buildDestinationMultiSelectData();
    }

    /**
     * Creates new package.
     *
     * @throws \Phalcon\Exception if package cannot be created
     * @return void
     */
    public function createAction()
    {
        // create pdf
        if ($this->request->isPost()) {
            $destination = $this->getDI()->get('Robinson\Backend\Models\Destination');
            $destination = $destination->findFirst($this->request->getPost('destinationId'));
            $pdf = '';
            $pdf2 = '';

            foreach ($this->request->getUploadedFiles() as $key => $file) {
                if ($file->getKey() === 'pdf') {
                    $pdf = $this->request->getUploadedFiles()[$key];
                } elseif ($file->getKey() === 'pdf2') {
                    $pdf2 = $this->request->getUploadedFiles()[$key];
                }
            }
            /* @var $package \Robinson\Backend\Models\Package */
            $package = $this->getDI()->get('Robinson\Backend\Models\Package');
            $package->setPackage($this->request->getPost('package'))
                ->setDestination($destination)
                ->setPrice($this->request->getPost('price'))
                ->setType($this->request->getPost('type', null, 0))
                ->setDescription($this->request->getPost('description'))
                ->setUploadedPdf($pdf)
                ->setStatus($this->request->getPost('status'));

            if ($pdf2) {
                $package->setUploadedPdf2($pdf2);
            }

            // add tabs, if any
            $tabs = array();

            foreach ($this->request->getPost('tabs') as $type => $description) {
                if (!$description) {
                    continue;
                }

                $tab = new \Robinson\Backend\Models\Tabs\Package();
                $tab->setDescription($description)
                    ->setType($type)
                    ->setTitle($tab->resolveTypeToTitle());
                $tabs[] = $tab;
            }

            $package->tabs = $tabs;

            // add tags, if any
            $tags = ($this->request->getPost('tags')) ? $this->request->getPost('tags') : array();
            $newtags = array();
            foreach ($tags as $type => $title) {
                if (!$title) {
                    continue;
                }

                $tag = new \Robinson\Backend\Models\Tags\Package();
                $tag->setType($type)
                    ->setTag($title);
                $newtags[] = $tag;
            }

            $package->tags = $newtags;

            if ($this->request->getPost('special')) {
                $package->setSpecial($this->request->getPost('special'));
            }

            if (!$package->create()) {
                $this->log->log(implode(';', $package->getMessages()), \Phalcon\Logger::ERROR);
                var_dump($package->getMessages());
                ob_flush();
                throw new \Phalcon\Exception('Unable to create new package.');
            }
            
            return $this->response->redirect(
                array(
                    'for' => 'admin-update',
                    'controller' => 'package',
                    'action' => 'update',
                    'id' => $package->getPackageId(),
                )
            );
        }

        $this->view->tags = $this->getDI()->getShared('config')->application->package->tags->toArray();
        $this->view->tabs = $this->getDI()->getShared('config')->application->package->tabs->toArray();
        $this->view->select = $this->buildDestinationMultiSelectData();
    }

    /**
     * Updates existing package.
     *
     * @throws \Phalcon\Exception if package cannot be updated
     *
     * @return void
     */
    public function updateAction()
    {
        set_time_limit(300);
        
        /* @var $package \Robinson\Backend\Models\Package */
        $package = $this->getDI()->get('Robinson\Backend\Models\Package');
        /* @var $package \Robinson\Backend\Models\Package */
        $package = $package->findFirst($this->dispatcher->getParam('id'));

        if ($package->getPdf2()) {
            $this->view->pdf = $this->getDI()->get(
                'Robinson\Backend\Models\Pdf',
                array(
                    $this->fs,
                    $package,
                    '/pdf/package',
                    2,
                )
            );
        }

        
        if ($this->request->isPost()) {
            $destination = $this->getDI()->get('Robinson\Backend\Models\Destination');
            $destination = $destination->findFirst($this->request->getPost('destinationId'));
            $package->setPackage($this->request->getPost('package'))
                ->setDestination($destination)
                ->setPrice($this->request->getPost('price'))
                ->setType($this->request->getPost('type'))
                ->setDescription($this->request->getPost('description'))
                ->setStatus($this->request->getPost('status'));

            $package->updateTabs($this->request->getPost('tabs', null, array()));
            
            // sort?
            $sort = $this->request->getPost('sort');
            
            if ($sort) {
                $this->sort($package, $sort);
            }
            
            // titles?
            $titles = $this->request->getPost('title');
            if ($titles) {
                $this->setImageTitles($package, $titles);
            }


            // tags
            $tags = ($this->request->getPost('tags')) ?: array();
            $package->updateTags($tags);
            
            $images = array();
            $files = $this->request->getUploadedFiles();
            
            foreach ($files as $file) {
                if ($file->getKey() === 'pdf') {
                    $package->setUploadedPdf($file);
                    continue;
                }

                if ($file->getKey() === 'pdf2') {
                    $package->setUploadedPdf2($file);
                    continue;
                }
                
                /* @var $packageImage \Robinson\Backend\Models\Images\Package */
                $packageImage = $this->getDI()->get('Robinson\Backend\Models\Images\Package');
                $packageImage->createFromUploadedFile($file)
                    ->setTitle($file->getName());

                $images[] = $packageImage;
            }
            
            if ($images) {
                $package->images = $images;
            }

            if ($this->request->getPost('special')) {
                $package->setSpecial($this->request->getPost('special'));
            }
            
            if (!$package->update()) {
                $this->log->log(implode(';', $package->getMessages()), \Phalcon\Logger::ERROR);
                var_dump($package->getMessages());
                ob_flush();
                throw new \Phalcon\Exception('Unable to update package #' . $package->getPackageId());
            }

            $package->refresh();
            $this->tag->setDefault('special', $package->getSpecial());
           
        }
        
        $tabs = $package->getTabs();
        foreach ($tabs as $tab) {
            $this->tag->setDefault('tabs[' . $tab->getType() . ']', $tab->getDescription());
        }

        foreach ($package->getTags() as $tag) {
            $this->tag->setDefault('tags[' . $tag->getType() . ']', $tag->getTag());
        }

        $this->tag->setDefault('type', $package->getType());
        $this->tag->setDefault('destinationId', $package->getDestination()->getDestinationId());
        $this->tag->setDefault('package', $package->getPackage());
        $this->tag->setDefault('price', $package->getPrice());
        $this->tag->setDefault('description', $package->getDescription());
        $this->tag->setDefault('status', $package->getStatus());

        $this->view->tabs = $this->getDI()->getShared('config')->application->package->tabs->toArray();
        $this->view->tags = $this->getDI()->getShared('config')->application->package->tags->toArray();

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

        $pdfType = ($this->request->getQuery('pdfType')) ? (int) $this->request->getQuery('pdfType')
            : \Robinson\Backend\Models\Pdf::PDF_FIRST;


        /* @var $pdf \Robinson\Backend\Models\Pdf */
        $pdf = $this->getDI()->get(
            'Robinson\Backend\Models\Pdf',
            array(
                $this->fs, $package, $this->config->application->packagePdfPath, $pdfType
            )
        );
        
        echo $pdf->getHtmlDocument($this->config->application->packagePdfWebPath)->saveHTML();
    }
    
    /**
     * Deletes package image. Outputs JSON.
     * 
     * @return string JSON response
     */
    public function deleteImageAction()
    {
        $image = \Robinson\Backend\Models\Images\Package::findFirst($this->request->getPost('id'));
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
        $categories = \Robinson\Backend\Models\Category::find(
            array(
            'order' => 'category',
            )
        );
        
        // build select
        $select = array();
        foreach ($categories as $category) {
            $select[$category->getCategory()] = array();
            
            foreach ($category->getDestinations() as $destination) {
                $select[$category->getCategory()][$destination->getDestinationId()] = $destination->getDestination();
            }
        }
        
        return $select;
    }
    
    /**
     * Sorts package images order.
     * 
     * @param \Robinson\Backend\Models\Package $package package model
     * @param array                            $sort    new order
     * 
     * @return true
     */
    protected function sort(\Robinson\Backend\Models\Package $package, array $sort)
    {
        $images = \Robinson\Backend\Models\Images\Package::find(
            array(
            'packageId = ' . $package->getPackageId(),
            )
        );

        foreach ($images as $image) {
            $image->setSort($sort[$image->getImageId()]);
            $image->update();
        }
        
        return true;
    }
    
    /**
     * Sets image titles.
     * 
     * @param \Robinson\Backend\Models\Package $package package model
     * @param array                            $titles  new titles
     * 
     * @return bool
     */
    protected function setImageTitles(\Robinson\Backend\Models\Package $package, array $titles)
    {
        $images = \Robinson\Backend\Models\Images\Package::find(
            array(
            'packageId = ' . $package->getPackageId(),
            )
        );

        foreach ($images as $image) {
            $image->setTitle($titles[$image->getImageId()]);
            $image->update();
        }
        
        return true;
    }
}
