<?php
namespace Robinson\Backend\Models\Tabs;
class Package extends \Robinson\Backend\Models\Tabs\Tabs
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
        $this->belongsTo('packageId', 'Robinson\Backend\Models\Package', 'packageId', array
        (
            'alias' => 'package',
        ));
    }

}