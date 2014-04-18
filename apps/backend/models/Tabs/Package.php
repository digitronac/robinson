<?php
namespace Robinson\Backend\Models\Tabs;

class Package extends \Robinson\Backend\Models\Tabs\Tabs
{
    const TYPE_PROGRAMME = 1;
    const TYPE_CONDITIONS = 2;
    const TYPE_AVIO = 3;
    const TYPE_BUS = 4;
    const TYPE_ANNOTATION = 5;
    
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
        $this->belongsTo(
            'packageId',
            'Robinson\Backend\Models\Package',
            'packageId',
            array('alias' => 'package')
        );
    }
    
    /**
     * Sets packageId.
     * 
     * @param int $packageId fk
     * 
     * @return \Robinson\Backend\Models\Tabs\Package
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
