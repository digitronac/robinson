<?php
/**
 * Date: 2/3/14
 * Time: 11:46 PM
 */
namespace Robinson\Frontend\Model;

class Destination extends \Phalcon\Mvc\Model
{
    const STATUS_INVISIBLE = 0;
    const STATUS_VISIBLE = 1;

    protected static $statusMessages = array
    (
        self::STATUS_INVISIBLE => 'nevidljiv',
        self::STATUS_VISIBLE => 'vidljiv',
    );

    protected $destinationId;

    protected $destination;

    protected $description;

    protected $status;

    protected $createdAt;

    protected $updatedAt;

    protected $categoryId;


    /**
     * Initialization method.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSource('destinations');

        $this->belongsTo('categoryId', 'Robinson\Frontend\Model\Category', 'categoryId', array
        (
           'alias' => 'category',
        ));

        $this->hasMany('destinationId', 'Robinson\Frontend\Model\Package', 'destinationId', array
        (
            'alias' => 'packages',
        ));
    }

    /**
     * Getter method for destination name.
     *
     * @param bool $escapeHtml flag
     *
     * @return string
     */
    public function getDestination($escapeHtml = true)
    {
        return $this->getDI()->getShared('escaper')->escapeHtml($this->destination);
    }

    public function getUri()
    {
        $filter = new \Robinson\Frontend\Filter\Unaccent();
        return '/' . $filter->filter(\Phalcon\Tag::friendlyTitle($this->category->getCategory())) . '/' . $filter->filter(\Phalcon\Tag::friendlyTitle($this->destination)) . '/' . $this->destinationId;
    }

    public function getDestinationId()
    {
        return (int) $this->destinationId;
    }
}
