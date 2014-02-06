<?php
namespace Robinson\Frontend\Model;
class Category extends \Phalcon\Mvc\Model
{
    const STATUS_INVISIBLE = 0;
    const STATUS_VISIBLE = 1;

    protected static $statusMessages = array
    (
        self::STATUS_INVISIBLE => 'nevidljiv',
        self::STATUS_VISIBLE => 'vidljiv',
    );

    protected $categoryId;

    protected $category;

    protected $description;

    protected $status;

    protected $createdAt;

    protected $updatedAt;

    /**
     * Initializaton method.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSource('categories');
    }

    /**
     * Getter method for category name.
     *
     * @param bool $escapeHtml flag
     *
     * @return string
     */
    public function getCategory($escapeHtml = true)
    {
        return $this->getDI()->getShared('escaper')->escapeHtml($this->category);
    }
}