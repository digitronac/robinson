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
        $this->view->destination = \Robinson\Frontend\Model\Destination::findFirst(
            'status = ' . \Robinson\Frontend\Model\Destination::STATUS_VISIBLE . ' AND destinationId = ' .
            (int) $this->dispatcher->getParam('id')
        );

        $this->view->categoryId = $this->view->destination->category->getCategoryId();
        $this->view->destinationId = $this->view->destination->getDestinationId();
        $this->tag->prependTitle($this->view->destination->getDestination() . ' - ');
    }
}
