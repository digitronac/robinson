<?php
namespace Robinson\Backend\Models\Images;
class Package extends \Robinson\Backend\Models\Images\Images
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
        return $this->packageImageId;
    }
    
    /**
     * Returns path to package images on filesystem.
     * 
     * @return string path to package images on filesystem
     */
    public function getImagesPath()
    {
        if ($this->getDI()->has('config'))
        {
            return realpath($this->getDI()->getShared('config')->application->packageImagesPath);
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
        $this->belongsTo('packageId', 'Robinson\Backend\Models\Package', 'packageId', array
        (
            'alias' => 'package',
        ));
        
        $this->setImageType(self::IMAGE_TYPE_PACKAGE);
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

}