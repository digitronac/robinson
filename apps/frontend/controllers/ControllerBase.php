<?php

namespace Robinson\Frontend\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function initialize()
    {
        \Phalcon\Tag::setTitle('robinson.rs');
    }
}