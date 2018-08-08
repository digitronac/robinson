<?php
namespace Robinson\Frontend\Controllers;

class CategoryController extends ControllerBase
{
    public function indexAction()
    {
        /**  @var \Phalcon\Translate\Adapter\NativeArray $this->view->translate */
        $this->view->translate = $this->getDI()['translate'];
        $categories = $this->getDI()->get('Robinson\Frontend\Model\Category');
        $this->view->category = $categories->findFirst(
            'status = ' . \Robinson\Frontend\Model\Category::STATUS_VISIBLE .
            ' AND categoryId = ' . (int) $this->dispatcher->getParam('id')
        );

        $this->view->categoryId = $this->view->category->getCategoryId();
        $this->tag->prependTitle($this->view->category->getCategory());
        $this->view->metaDescription = \Phalcon\Tag::tagHtml('meta', array(
            'name' => 'description',
            'content' => $this->view->category->getCategory() . ' - ' . $this->getDI()->get('translate')->query($this->view->season->name),
        ));

        if ($this->view->category->isEnglish()) {
            $this->view->setMainView('layouts/english');
            $this->view->pick('insideserbia/category');
        }
    }
}
