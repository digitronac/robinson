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
    }
}
