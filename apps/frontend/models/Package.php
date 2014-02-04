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
    

    public function initialize()
    {
        $this->setSource('packages');
        $this->belongsTo('destinationId', 'Robinson\Frontend\Model\Destination', 'destinationId', array
        (
            'alias' => 'destination', 
        ));

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
     * Gets package status.
     * 
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->status;
    }
    
    /**
     * Status helper method.
     * 
     * @return bool 
     */
    public function isNotVisible()
    {
        return ($this->getStatus() === self::STATUS_INVISIBLE);
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
     * Gets package uri.
     *
     * @return string
     */
    public function getUri()
    {
        return '';
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

}