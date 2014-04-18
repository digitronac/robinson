<?php
namespace Robinson\Frontend\Model\Images;

class Package extends \Robinson\Frontend\Model\Images\Images
{
    protected $packageImageId;
    
    protected $packageId;
    
    protected $title;
    
    /**
     * Get packageId 
     * 
     * @return int packageId
     */
    public function getBelongsToId()
    {
        return (int) $this->packageId;
    }
    
    /**
     * Get packageImageId.
     * 
     * @return int packageImageId
     */
    public function getImageId()
    {
        return (int) $this->packageImageId;
    }
    
    /**
     * Returns path to package images on filesystem.
     * 
     * @return string path to package images on filesystem
     */
    public function getImagesPath()
    {
        if ($this->getDI()->has('config')) {
            return $this->getDI()->getShared('config')->application->packageImagesPath;
        }
    }
    
    /**
     * Initialization method.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->setSource('package_images');
        $this->belongsTo(
            'packageId',
            'Robinson\Backend\Model\Package',
            'packageId',
            array(
                'alias' => 'package',
            )
        );

        $this->setImageType(self::IMAGE_TYPE_PACKAGE);
    }
    
    /**
     * Executed on construct.
     * 
     * @return bool
     */
    public function onConstruct()
    {
        $this->setImageType(self::IMAGE_TYPE_PACKAGE);
        return parent::onConstruct();
    }
    
    /**
     * Set image title.
     * 
     * @param string $title image title
     * 
     * @return \Robinson\Backend\Models\Images\Package
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    /**
     * Gets image title.
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Set packageId
     * 
     * @param int $packageId packageId
     * 
     * @return \Robinson\Backend\Models\Images\Package
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
}
