<?php
namespace Robinson\Frontend\Controllers;

class PackageController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->package = \Robinson\Frontend\Model\Package::findFirst(
            'status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE . ' AND packageId = ' .
            (int) $this->dispatcher->getParam('id')
        );

        $this->view->categoryId = $this->view->package->destination->category->getCategoryId();
        $this->tag->prependTitle($this->view->package->getPackage() . ' - ');
    }
} 