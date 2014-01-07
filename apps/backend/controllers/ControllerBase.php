<?php
namespace Robinson\Backend\Controllers;

class ControllerBase extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        $this->tag->setTitle('Robinson Admin');
    }
}