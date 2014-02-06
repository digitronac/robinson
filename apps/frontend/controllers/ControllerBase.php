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
                'order' => 'category ASC',
            )
        );
        \Phalcon\Tag::setTitle('robinson.rs');
    }
}