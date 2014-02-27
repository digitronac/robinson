<?php
namespace Robinson\Frontend\Controllers;

class DestinationController extends ControllerBase
{
    /**
     * Destination page.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->type = $this->request->getQuery('type');

        $this->view->destination = \Robinson\Frontend\Model\Destination::findFirst(
            'status = ' . \Robinson\Frontend\Model\Destination::STATUS_VISIBLE . ' AND destinationId = ' .
            (int) $this->dispatcher->getParam('id')
        );

        $this->view->categoryId = $this->view->destination->category->getCategoryId();
        $this->view->destinationId = $this->view->destination->getDestinationId();
        $this->tag->setDefault('type', $this->view->type);
        $this->tag->prependTitle($this->view->destination->getDestination() . ' - ');
    }
}
