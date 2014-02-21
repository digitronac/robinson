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
var_dump($this->dispatcher->getParam('id'));
        die();
        $this->view->pdf = new \Robinson\Frontend\Model\Pdf(
            $this->fs,
            $this->view->package,
            $this->config->application->packagePdfPath
        );

        $this->view->categoryId = $this->view->package->destination->category->getCategoryId();
        $this->tag->prependTitle($this->view->package->getPackage() . ' - ');
    }

    public function pdfAction()
    {
        $this->view->package = \Robinson\Frontend\Model\Package::findFirst(
            'status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE . ' AND packageId = ' .
            $this->dispatcher->getParam('id')
        );

        /* @var $pdf \Robinson\Frontend\Model\Pdf */
        $pdf = $this->getDI()->get('Robinson\Frontend\Model\Pdf', array(
            $this->fs,
            $this->view->package,
            $this->config->application->packagePdfPath
        ));

        return $this->response->setContent(
            $pdf->getHtmlDocument($this->config->application->packagePdfWebPath)
                ->saveHTML()
        );
    }
} 