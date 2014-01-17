<?php
namespace Robinson\Backend\Models\Images;
class Destination extends \Robinson\Backend\Models\Images\Images
{
    protected $destinationImageId;
    
    protected $destinationId;
    
    /**
     * Gets PK.
     * 
     * @return int
     */
    public function getImageId()
    {
        return (int) $this->destinationImageId;
    }
    
    /**
     * Returns path where images are saved.
     * 
     * @return string
     */
    public function getImagesPath()
    {
        if ($this->getDI()->has('config'))
        {
            return realpath($this->getDI()->getShared('config')->application->destinationImagesPath);
        }
    }

    /**
     * Initializion method.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->setSource('DestinationImages');
        $this->belongsTo('destinationId', 'Robinson\Backend\Models\Destinations', 'destinationId');
        $this->hasMany('destinationId', 'Robinson\Backend\Models\Package', array
        (
            'alias' => 'packages',
        ));
        
        $this->setImageType(self::IMAGE_TYPE_DESTINATION);
    }
    
    /**
     * Sets destinationId.
     * 
     * @param int $destinationId destinationId
     * 
     * @return \Robinson\Backend\Models\Images\Destination
     */
    public function setDestinationId($destinationId)
    {
        $this->destinationId = (int) $destinationId;
        return $this;
    }

    /**
     * Id to which this model belongs.
     * 
     * @return int
     */
    public function getBelongsToId()
    {
        return $this->destinationId;
    }

}