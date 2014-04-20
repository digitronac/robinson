<?php

namespace Robinson\Frontend\Controllers;

class IndexController extends ControllerBase
{

    /**
     * Index page
     *
     * @return void
     */
    public function indexAction()
    {
        /** @var $package \Robinson\Frontend\Model\Package */
        $package = $this->getDI()->get('Robinson\Frontend\Model\Package');
        $this->view->lastMinutePackages = $package->findLastMinute();

        /** @var $package \Robinson\Frontend\Model\Package */
        $package = $this->getDI()->get('Robinson\Frontend\Model\Package');
        $this->view->latestPackages = $package->find(
            array(
                'status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE,
                'order' => 'createdAt DESC',
                'limit' => 4,
            )
        );

        $this->view->popularPackages = $package->findPopular(4);

        $category = $this->getDI()->get('Robinson\Frontend\Model\Category');
        $tabs = array();
        //$this->view->categoryTabs = $category->findByIds($this->config->application->display->tabs->index->toArray());
        foreach ($this->config->application->display->tabs->first->index->toArray() as $key => $tab) {
            $stdClass = new \stdClass();
            $stdClass->name = $tab;
            $stdClass->category = $category->findFirst(array(
                'conditions' => "categoryId = $key AND status = 1",
            ));
            $tabs[] = $stdClass;
        }

        $this->view->firstTabs = $tabs;

        $tabs = array();
        foreach ($this->config->application->display->tabs->second->index->toArray() as $key => $tab) {
            $stdClass = new \stdClass();
            $stdClass->name = $tab;
            $category = $category->findFirst(array(
                    'conditions' => "categoryId = $key AND status = 1",
                    'limit' => 8,
            ));

            if (!$category) {
                continue;
            }
            $packages = $category->getPackagesDirectly();
            $stdClass->packages = $packages;
            $tabs[] = $stdClass;
        }

        $this->view->secondTabs = $tabs;
    }
}
