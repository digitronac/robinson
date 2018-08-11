<?php
namespace Robinson\Frontend\Controllers;

class PageController extends ControllerBase
{
    /**
     *  Displays page.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->page = \Robinson\Frontend\Model\Page::findFirst((int) $this->request->getQuery('pageId', 'int'));
        if (is_english()) {
            $this->view->setMainView('layouts/english');
        }
    }
}
