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
        $this->hasMany(
            'categoryId',
            'Robinson\Frontend\Model\Images\Category',
            'categoryId',
            array
            (
                'alias' => 'images',
                'reusable' => true,
            )
        );

        $this->hasMany(
            'categoryId',
            'Robinson\Frontend\Model\Destination',
            'categoryId',
            array
            (
                'alias' => 'destinations',
                'reusable' => true,
            )
        );
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

    /**
     * Uri to category.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->slug .  '/' . $this->categoryId;
    }

    public function isEnglish()
    {
        return ((int) $this->categoryId === (int) $this->getDI()['config']->application->insideserbia->categoryId);
    }

    /**
     * Gets category description.
     *
     * @param null|int $limit char limit
     *
     * @return string
     */
    public function getDescription($limit = null)
    {
        if (!$limit) {
            return $this->description;
        }

        return \HtmlTruncator\Truncator::truncate($this->description, $limit);
    }

    /**
     * Gets category id.
     *
     * @return int
     */
    public function getCategoryId()
    {
        return (int) $this->categoryId;
    }

    /**
     * Gets related images.
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getImages()
    {
        return $this->getRelated(
            'images',
            array(
                'order' => 'sort ASC',
            )
        );
    }

    /**
     * Gets related destinations.
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getDestinations()
    {
        return $this->getRelated(
            'destinations',
            array
            (
                'status = ' . \Robinson\Frontend\Model\Destination::STATUS_VISIBLE,
                'order' => 'destination ASC',
            )
        );
    }

    /**
     * Returns packages that belong to this category.
     *
     * @param int $limit limit
     *
     * @return \Robinson\Frontend\Model\Package
     */
    public function getPackagesDirectly($limit)
    {
        $catId = $this->getCategoryId();
        $sql =
        "
            SELECT packages.* FROM \Robinson\Frontend\Model\Package as packages
            INNER JOIN \Robinson\Frontend\Model\Destination as destinations
            WHERE destinations.categoryId = $catId
            AND packages.status = 1
            ORDER BY RAND()
            LIMIT $limit
        ";
        $query = $this->getModelsManager()->createQuery($sql);
        $query->cache(array(
            'key' => 'find-random-packages-by-categoryId-' . $catId,
        ));
        return $query->execute();
    }
}
