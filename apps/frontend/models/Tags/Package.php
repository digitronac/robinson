<?php
namespace Robinson\Frontend\Model\Tags;

class Package extends Tag
{
    const TYPE_FIRST_MINUTE = 1;
    const TYPE_LAST_MINUTE = 2;
    const TYPE_POPULAR = 3;

    protected $packageTagId;

    protected $packageId;

    protected $order;

    /**
     * Initialization method.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSource('package_tags');
        $this->belongsTo(
            'packageId',
            'Robinson\Frontend\Model\Package',
            'packageId',
            array(
                'alias' => 'package',
            )
        );
    }

    /**
     * Resolves tag type to tag title from configuration.
     *
     * @param int $type tag type
     *
     * @return string tag title
     */
    public function resolveTypeToTagTitle($type)
    {
        return $this->getDI()->getShared('config')->application->package->tags->toArray[$type];
    }
}
