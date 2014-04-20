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

    /**
     * Getter method for title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Getter method for description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
