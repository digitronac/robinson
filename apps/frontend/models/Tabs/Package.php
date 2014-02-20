<?php
namespace Robinson\Frontend\Model\Tabs;
class Package extends \Robinson\Frontend\Model\Tabs\Tabs
{
    const TYPE_PROGRAMME = 1;
    const TYPE_CONDITIONS = 2;
    const TYPE_AVIO = 3;
    const TYPE_BUS = 4; 
    
    protected $packageTabId;
    
    protected $packageId;
    
    /**
     * Initialization method.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->setSource('package_tabs');
        $this->belongsTo('packageId', 'Robinson\Frontend\Model\Package', 'packageId', array
        (
            'alias' => 'package',
        ));
    }
    
    /**
     * Sets packageId.
     * 
     * @param int $packageId fk
     * 
     * @return \Robinson\Frontend\Model\Tabs\Package
     */
    public function setPackageId($packageId)
    {
        $this->packageId = (int) $packageId;
        return $this;
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
     * Returns tab title based on tab type.
     * 
     * @return string title
     */
    public function resolveTypeToTitle()
    {
        return ($this->getDI()->getShared('config')->application->package->tabs->toArray()[$this->getType()]);
    }

}