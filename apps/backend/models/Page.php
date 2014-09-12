<?php
namespace Robinson\Backend\Models;

/**
 * Class Page.
 *
 * @package Robinson\Backend\Models
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
     * @var string
     */
    private $slug;

    /**
     * Initialization.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSource('pages');
        $this->addBehavior(
            new \Phalcon\Mvc\Model\Behavior\Timestampable(
                array(
                    'beforeValidationOnCreate' => array(
                        'field' => 'createdAt',
                        'format' => 'Y-m-d H:i:s',
                    ),
                )
            )
        );

        $this->addBehavior(
            new \Phalcon\Mvc\Model\Behavior\Timestampable(
                array(
                    'beforeValidationOnCreate' => array(
                        'field' => 'updatedAt',
                        'format' => 'Y-m-d H:i:s',
                    ),
                )
            )
        );

        $this->addBehavior(
            new \Phalcon\Mvc\Model\Behavior\Timestampable(
                array(
                    'beforeValidationOnUpdate' => array(
                        'field' => 'updatedAt',
                        'format' => 'Y-m-d H:i:s',
                    ),
                )
            )
        );
    }

    /**
     * Title setter method.
     *
     * @param string $title title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
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
     * Body setter method.
     *
     * @param string $body body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
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
     * Slug getter method.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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

    /**
     * OnBeforeValidation.
     *
     * @return void
     */
    public function beforeValidation()
    {
        $this->slug = $this->getDI()->get('tag')->friendlyTitle($this->title);
    }
}
