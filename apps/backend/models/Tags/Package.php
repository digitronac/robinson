<?php
namespace Robinson\Backend\Models\Tags;
class Package extends Tag
{
    const TYPE_FIRST_MINUTE = 1;
    const TYPE_LAST_MINUTE = 2;

    protected $packageTagId;

    protected $packageId;

    /**
     * Initialization method.
     *
     * @return void
     */
    public function initialize()
    {
        $this->setSource('package_tags');
        $this->belongsTo('packageId', 'Robinson\Backend\Models\Package', 'packageId', array
        (
            'alias' => 'package',
        ));
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

    /**
     * Sets packageId.
     *
     * @param int $packageId packageId
     *
     * @return $this
     */
    public function setPackageId($packageId)
    {
        $this->packageId = (int) $packageId;
        return $this;
    }

    /**
     * Gets package id.
     *
     * @return int
     */
    public function getPackageId()
    {
        return (int) $this->packageId;
    }

    /**
     * Gets package tag id.
     *
     * @return int
     */
    public function getPackageTagId()
    {
        return $this->packageTagId;
    }

}