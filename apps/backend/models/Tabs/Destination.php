<?php
namespace Robinson\Backend\Models\Tabs;

class Destination extends \Robinson\Backend\Models\Tabs\Tabs
{
    const TYPE_APARTMENT = 1;
    const TYPE_HOTEL = 2;
    const TYPE_EXCURSION = 3;
    
    protected $destinationTabId;
    
    protected $destinationId;
    
    /**
     * Initialization method.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->setSource('destination_tabs');
        $this->belongsTo(
            'destinationId',
            'Robinson\Backend\Models\Destination',
            'destinationId',
            array('alias' => 'destination')
        );
    }
    
    /**
     * Sets destinationId.
     * 
     * @param int $destinationId fk
     * 
     * @return \Robinson\Backend\Models\Tabs\Destination
     */
    public function setDestinationId($destinationId)
    {
        $this->destinationId = (int) $destinationId;
        return $this;
    }
    
    /**
     * Gets destinationId.
     * 
     * @return int
     */
    public function getDestinationId()
    {
        return (int) $this->destinationId;
    }
    
    /**
     * Returns tab title based on tab type.
     * 
     * @return string title
     */
    public function resolveTypeToTitle()
    {
        return ($this->getDI()->getShared('config')->application->destination->tabs->toArray()[$this->getType()]);
    }
}
