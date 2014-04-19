<?php
namespace Robinson\Frontend\Model\Tabs;

abstract class Tabs extends \Phalcon\Mvc\Model
{
    protected $title;
   
    protected $description;
    
    protected $type;
    
    protected $createdAt;
    
    protected $updatedAt;
    
    /**
     * Initialization method. Must be overriden.
     * 
     * @return void
     */
    abstract public function initialize();
}
