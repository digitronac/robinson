<?php
namespace Robinson\Frontend\Model\Tabs;

class Package extends \Robinson\Frontend\Model\Tabs\Tabs
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
            'Robinson\Frontend\Model\Package',
            'packageId',
            array
            (
                'alias' => 'package',
            )
        );
    }
}
