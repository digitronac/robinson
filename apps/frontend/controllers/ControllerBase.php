<?php

namespace Robinson\Frontend\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function initialize()
    {
        $this->view->destinations = \Robinson\Frontend\Model\Destination::find(
            array
            (
                'status = ' . \Robinson\Frontend\Model\Destination::STATUS_VISIBLE,
                'order' => 'destination ASC',
            )
        );
        $this->view->lowPricePackage = \Robinson\Frontend\Model\Package::findFirst(
            array
            (
                'status = ' . \Robinson\Frontend\Model\Destination::STATUS_VISIBLE . ' AND price != 0',
                'order' => 'price ASC',
            )
        );
        $this->view->categories = \Robinson\Frontend\Model\Category::find(
            array
            (
                'status = ' . \Robinson\Frontend\Model\Category::STATUS_VISIBLE,
                'order' => 'categoryId ASC',
                'limit' => 8,
            )
        );

        $this->view->randomPackages = \Robinson\Frontend\Model\Package::find(array(
            'status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE,
            'order' => 'RAND()',
            'limit' => 10,
        ));

        $this->tag->setTitle('robinson.rs');
    }
}