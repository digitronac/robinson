<?php

namespace Robinson\Frontend\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    protected $destinations;

    public function initialize()
    {
        $this->destinations = \Robinson\Frontend\Model\Destination::find(
            array
            (
                'condition' => 'status = ' . \Robinson\Frontend\Model\Destination::STATUS_VISIBLE,
                'order' => 'destination DESC',
            )
        );
        \Phalcon\Tag::setTitle('robinson.rs');
    }
}