<?php
namespace Robinson\Backend\Models;
class Destination extends \Robinson\Backend\Models\Images\Images
{
    protected $destinationImageId;
    
    protected $destinationId;
    
    public function getImageId()
    {
        return $this->imageId;
    }

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
    }
    
    public function setDestinationId()
    {
        
    }
}