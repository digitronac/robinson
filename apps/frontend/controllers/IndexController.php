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
        /*$this->view->latestPackages = $package->find(
            array(
                'status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE,
                'order' => 'createdAt DESC',
                'limit' => 4,
            )
        );*/
        $this->view->hotPackages = $package->findHot(4);

        $this->view->popularPackages = $package->findPopular(4);

        $this->view->topTabs = $this->makeTopTabs();
        $this->view->bottomTabs = $this->makeBottomTabs(8);
    }

    /**
     * Creates array of objects that contain data for building landing page bottom tabs.
     *
     * @param int $limit limit
     *
     * @return array tabs data
     */
    protected function makeBottomTabs($limit)
    {
        $category = $this->getDI()->get('Robinson\Frontend\Model\Category');
        $tabs = array();
        foreach ($this->config->application->tabs->landing->bottom->toArray() as $key => $tab) {
            $stdClass = new \stdClass();
            $stdClass->name = $tab;
            $category = $category->findFirst(array(
                    'conditions' => "categoryId = $key AND status = 1",
                    'limit' => $limit,
                ));

            if (!$category) {
                continue;
            }
            $packages = $category->getPackagesDirectly($limit);
            $stdClass->packages = $packages;
            $tabs[] = $stdClass;
        }

        return $tabs;
    }

    /**
     * Creates array of objects that contain data for building landing page top tabs.
     *
     * @return array tabs data
     */
    protected function makeTopTabs()
    {
        $category = $this->getDI()->get('Robinson\Frontend\Model\Category');
        $tabs = array();

        foreach ($this->config->application->tabs->landing->top->toArray() as $key => $tab) {
            $stdClass = new \stdClass();
            $stdClass->name = $tab;
            $stdClass->category = $category->findFirst(array(
                    'conditions' => "categoryId = $key AND status = 1",
            ));
            $tabs[] = $stdClass;
        }

        return $tabs;
    }
}
