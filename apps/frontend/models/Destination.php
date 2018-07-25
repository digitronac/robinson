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

        $this->belongsTo(
            'categoryId',
            'Robinson\Frontend\Model\Category',
            'categoryId',
            array(
                'alias' => 'category',
                'reusable' => true,
            )
        );

        $this->hasMany(
            'destinationId',
            'Robinson\Frontend\Model\Package',
            'destinationId',
            array(
                'alias' => 'packages',
                'reusable' => true,
            )
        );

        $this->hasMany(
            'destinationId',
            'Robinson\Frontend\Model\Images\Destination',
            'destinationId',
            array(
                'alias' => 'images',
                'reusable' => true,
            )
        );
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

    /**
     * Description getter method.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function isEnglish()
    {
        return in_array($this->categoryId, $this->getDI()['config']->application->insideserbia->categoryIds->toArray());
    }

    /**
     * Uri to destination.
     *
     * @return string
     */
    public function getUri()
    {
        $baseUrls = $this->getDI()->get('config')->application->baseUrls;
        $domain = $baseUrls->rsBaseUrl;
        if ($this->isEnglish()) {
            $domain = $baseUrls->enBaseUrl;
        }

        return $domain . '/' . $this->slug . '/' . $this->destinationId;
    }

    /**
     * DestinationId getter method.
     *
     * @return int
     */
    public function getDestinationId()
    {
        return (int) $this->destinationId;
    }

    /**
     * Related images getter method.
     *
     * @param array $params query params
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getImages(array $params = null)
    {
        return $this->getRelated('images', $params);
    }

    /**
     * Related packages getter method.
     *
     * @param null|int $type type
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getPackages($type = null)
    {
        if ($type) {
            return $this->getRelated(
                'packages',
                array(
                    'type = :type: AND status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE,
                    'bind' => array(
                        'type' => $type,
                    )
                )
            );
        }

        return $this->getRelated('packages', array('status = ' . \Robinson\Frontend\Model\Package::STATUS_VISIBLE));
    }

    /**
     * Returns featured packages.
     *
     * @return array
     */
    public function getFeaturedPackages()
    {
        $featured = [];
        $config = $this->getDI()->get('config');
        $featuredPackages = $config->application->destination->featuredPackages;
        if (!empty($featuredPackages->toArray()[$this->destinationId])) {
            foreach ($featuredPackages->toArray()[$this->destinationId] as $packageId) {
                array_push($featured, Package::findFirst((int) $packageId));
            }
        }
        return $featured;
    }

    /**
     * Finds main image of package (one with lowest sort number).
     *
     * @return Images\Package
     */
    public function getMainImage()
    {
        $images = $this->getImages(
            array(
                'order' => 'sort ASC',
                'limit' => 1,
            )
        );

        if (!$images->count()) {
            return;
        }

        /** @var $image \Robinson\Frontend\Model\Images\Destination */
        $image = $images[0];

        return $image;

    }

    /**
     * Returns truncated description.
     *
     * @return string
     */
    public function getShortDescription()
    {
        return self::truncateText($this->description, 230);
    }


    /**
     * Truncates text to given char limit.
     *
     * @param string $text  text which should be truncated
     * @param int    $limit char limit
     * @param string $break on which character should truncate happen
     * @param string $pad   padding string, will be appended on end of truncated string
     *
     * @return string truncated text
     */
    public static function truncateText($text, $limit, $break = ".", $pad = "...")
    {
        // Original PHP code by Chirp Internet: www.chirp.com.au
        // Please acknowledge use of this code by including this header

        $text = strip_tags($text);

        // return with no change if string is shorter than $limit
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        // is $break present between $limit and the end of the string?
        if (false !== ($breakpoint = mb_strpos($text, $break, $limit))) {
            if ($breakpoint < mb_strlen($text) - 1) {
                $text = mb_substr($text, 0, $breakpoint) . $pad;
            }
        }

        return trim($text);
    }
}
