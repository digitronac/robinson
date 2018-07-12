<?php
namespace Robinson\Frontend\Model;

class Package extends \Phalcon\Mvc\Model
{
    const STATUS_INVISIBLE = 0;
    const STATUS_VISIBLE = 1;

    const TYPE_UNDEFINED = 0;
    const TYPE_APARTMENT = 1;
    const TYPE_HOTEL = 2;
    
    protected static $statusMessages = array
    (
        self::STATUS_INVISIBLE => 'nevidljiv',
        self::STATUS_VISIBLE => 'vidljiv',
    );

    protected static $types = array
    (
        self::TYPE_UNDEFINED => 'nedefinisan',
        self::TYPE_APARTMENT => 'apartman',
        self::TYPE_HOTEL => 'hotel',
    );
    
    protected $packageId;
    
    protected $package;
    
    protected $description;
    
    protected $price;
    
    protected $pdf;
    
    protected $status;
    
    protected $createdAt;
    
    protected $updatedAt;
    
    protected $destinationId;

    protected $type = self::TYPE_UNDEFINED;

    protected $special;
    

    public function initialize()
    {
        $this->setSource('packages');
        $this->belongsTo(
            'destinationId',
            'Robinson\Frontend\Model\Destination',
            'destinationId',
            array(
                'alias' => 'destination',
                'reusable' => true,
            )
        );

        $this->hasMany(
            'packageId',
            'Robinson\Frontend\Model\Tabs\Package',
            'packageId',
            array(
                'alias' => 'tabs',
                'reusable' => true,
            )
        );

        $this->hasMany(
            'packageId',
            'Robinson\Frontend\Model\Tags\Package',
            'packageId',
            array(
                'alias' => 'tags',
                'reusable' => true,
            )
        );

        $this->hasMany(
            'packageId',
            'Robinson\Frontend\Model\Images\Package',
            'packageId',
            array(
                'alias' => 'images',
                'reusable' => true,
            )
        );
    }

    /**
     * Gets packageId.
     * 
     * @return int
     */
    public function getPackageId()
    {
        return (int) $this->packageId;
    }


    /**
     * Gets package name.
     * 
     * @return string package name
     */
    public function getPackage()
    {
        return $this->package;
    }
    

    /**
     * Gets package description.
     * 
     * @return string package description
     */
    public function getDescription()
    {
        return $this->description;
    }
    

    /**
     * Gets package lowest price.
     * 
     * @return int price lowest package price
     */
    public function getPrice()
    {
        return $this->price;
    }
    

    /**
     * Gets pdf base file name.
     * 
     * @return string pdf's base file name
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * Not implemented yet.
     * @return void
     */
    public function getPdf2()
    {

    }

    /**
     * Retrieves destination to which package belongs to.
     * 
     * @return \Robinson\Backend\Models\Destination
     */
    public function getDestination()
    {
        return $this->getRelated('destination');
    }

    public function isEnglish()
    {
        return $this->getDestination()->isEnglish();
    }

    /**
     * Gets package images.
     *
     * @param array|null $params additional params
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getImages(array $params = null)
    {
        if (!$params) {
            return $this->getRelated(
                'images',
                array(
                    'order' => 'sort ASC',
                )
            );
        }

        return $this->getRelated('images', $params);
    }

    /**
     * Finds main image of package (one with sort number 1).
     *
     * @return Images\Package
     */
    public function getMainImage()
    {
        $images = $this->getImages(
            array(
                'conditions' => '[sort] = 1',
                'limit' => 1,
            )
        );

        // no images? try lowest sort number
        if (!$images->count()) {
            $images = $this->getImages(
                array(
                    'order' => 'sort ASC',
                    'limit' => 1,
                )
            );
        }

        // ...
        if (!$images->count()) {
            return;
        }

        /** @var $image \Robinson\Frontend\Model\Images\Package */
        $image = $images[0];

        return $image;
    }

    /**
     * Get package type.
     *
     * @return int
     */
    public function getType()
    {
        return (int) $this->type;
    }

    /**
     * Special getter method.
     *
     * @return string
     */
    public function getSpecial()
    {
        return $this->special;
    }

    /**
     * Uri to package.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->slug . '/' . $this->packageId;
    }

    /**
     * Gets package tabs.
     *
     * @param array $params additional criteria
     *
     * @return \Phalcon\Mvc\Model\Resultset\Simple
     */
    public function getTabs(array $params = null)
    {
        return $this->getRelated('tabs', $params);
    }

    /**
     * Returns human readable status text.
     * 
     * @return string
     */
    public static function getStatusMessages()
    {
        return self::$statusMessages;
    }

    /**
     * Returns human readable type text.
     *
     * @return string
     */
    public static function getTypeMessages()
    {
        return self::$types;
    }

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

    /**
     * Finds last minute packages.
     *
     * @return mixed
     */
    public function findLastMinute()
    {
        $query = $this->getModelsManager()->createQuery(
            'SELECT packages.* FROM Robinson\Frontend\Model\Package AS packages JOIN
            Robinson\Frontend\Model\Tags\Package as packageTags
            ON packages.packageId = packageTags.packageId
            WHERE packages.status = 1 AND packageTags.type = 2 ORDER BY packageTags.[order] ASC'
        );
        $query->cache(array(
            'key' => 'last-minute-packages',
        ));
        return $query->execute();
    }

    /**
     * Finds homepage packages.
     *
     * @return mixed
     */
    public function findHomepage()
    {
        $query = $this->getModelsManager()->createQuery(
            'SELECT packages.* FROM Robinson\Frontend\Model\Package AS packages JOIN
            Robinson\Frontend\Model\Tags\Package as packageTags
            ON packages.packageId = packageTags.packageId
            WHERE packages.status = 1 AND packageTags.type = 1 ORDER BY packageTags.[order] ASC'
        );
        $query->cache(array(
            'key' => 'homepage-packages',
        ));
        return $query->execute();
    }

    /**
     * Finds popular packages.
     *
     * @param int $limit record limit
     *
     * @return mixed
     */
    public function findPopular($limit)
    {
        $query = $this->getModelsManager()->createQuery(
            "SELECT packages.* FROM Robinson\Frontend\Model\Package AS packages JOIN
            Robinson\Frontend\Model\Tags\Package as packageTags ON packages.packageId = packageTags.packageId
            WHERE packages.status = 1 AND packageTags.type = 3 ORDER BY packageTags.[order] LIMIT $limit"
        );
        $query->cache(array(
            'key' => 'popular-packages',
        ));
        return $query->execute();
    }

    /**
     * Finds hot packages.
     *
     * @param int $limit
     *
     * @return mixed
     */
    public function findHot($limit)
    {
        $query = $this->getModelsManager()->createQuery(
            "SELECT packages.* FROM Robinson\Frontend\Model\Package AS packages JOIN
            Robinson\Frontend\Model\Tags\Package as packageTags ON packages.packageId = packageTags.packageId
            WHERE packages.status = 1 AND packageTags.type = 4 ORDER BY packageTags.[order] LIMIT $limit"
        );
        $query->cache(array(
            'key' => 'hot-packages',
        ));
        return $query->execute();
    }
}
