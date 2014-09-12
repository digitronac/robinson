<?php
namespace Robinson\Backend\Controllers;

/**
 * Class PageController.
 *
 * @package Robinson\Backend\Controllers
 */
class PageController extends \Robinson\Backend\Controllers\ControllerBase
{
    public function indexAction()
    {
        $pages = \Robinson\Backend\Models\Page::find(array('order' => 'title ASC'));
        $this->view->pages = $pages;
    }

    /**
     * Create.
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function createAction()
    {
        if ($this->request->getPost('title')) {
            $title = $this->request->getPost('title', 'trim');
            $body = $this->request->getPost('body', 'trim');
            /** @var \Robinson\Backend\Models\Page $page */
            $page = $this->getDI()->get('Robinson\Backend\Models\Page');
            $page->setTitle($title);
            $page->setBody($body);
            $page->create();
            return $this->response->redirect('/admin/page/update?pageId=' . $page->getPageId(), true);
        }
    }

    /**
     * Update.
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function updateAction()
    {
        $pageId = (int) $this->request->getQuery('pageId');
        /** @var \Robinson\Backend\Models\Page $page */
        $page = $this->getDI()->get('Robinson\Backend\Models\Page')->findFirst($pageId);
        $this->tag->setDefaults(array(
            'title' => $page->getTitle(),
            'body' => $page->getBody(),
        ));
        if ($this->request->getPost('title')) {
            $title = $this->request->getPost('title', 'trim');
            $body = $this->request->getPost('body', 'trim');
            $page->setTitle($title);
            $page->setBody($body);
            $page->update();
            return $this->response->redirect('/admin/page/update?pageId=' . $page->getPageId(), true);
        }
    }
}
