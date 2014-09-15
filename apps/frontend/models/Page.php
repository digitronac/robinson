<?php
namespace Robinson\Frontend\Model;

/**
 * Class Page.
 *
 * @package Robinson\Frontend\Model
 */
class Page extends \Phalcon\Mvc\Model
{
    /**
     * @var int
     */
    private $pageId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $body;

    /**
     * Initialization.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSource('pages');
    }

    /**
     * Title getter method.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Body getter method.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * PageId getter method.
     *
     * @return int
     */
    public function getPageId()
    {
        return (int) $this->pageId;
    }
}
