<?php

namespace Robinson\Frontend\Controllers;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $this->view->destinations = $this->destinations;
    }

}

