<?php
namespace Robinson\Frontend\Model\Images;

class Destination extends \Robinson\Frontend\Model\Images\Images
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
        return $this->getDI()->getShared('config')->application->destinationImagesPath;
    }

    /**
     * Initializion method.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->setSource('destination_images');
        $this->belongsTo(
            'destinationId',
            'Robinson\Frontend\Model\Destination',
            'destinationId',
            array(
                'alias' => 'destinations',
            )
        );
        
        $this->setImageType(self::IMAGE_TYPE_DESTINATION);
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
