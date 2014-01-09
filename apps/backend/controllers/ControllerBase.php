<?php
namespace Robinson\Backend\Controllers;

class ControllerBase extends \Phalcon\Mvc\Controller
{
    /**
     * Initialization method.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->tag->setTitle('Robinson Admin');
    }
}