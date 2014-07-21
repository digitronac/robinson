<?php
namespace Robinson\Frontend\Model\Images;

class Category extends \Robinson\Frontend\Model\Images\Images
{
    protected $categoryImageId;
    
    protected $categoryId;
    
    /**
     * Gets PK.
     * 
     * @return int
     */
    public function getImageId()
    {
        return (int) $this->categoryImageId;
    }
    
    /**
     * Returns path where images are saved.
     * 
     * @return string
     */
    public function getImagesPath()
    {
        return $this->getDI()->getShared('config')->application->categoryImagesPath;
    }

    /**
     * Initialization method.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->setSource('category_images');
        $this->belongsTo('categoryId', 'Robinson\Frontend\Model\Category', 'categoryId');
        $this->setImageType(self::IMAGE_TYPE_CATEGORY);
    }

    public function getBelongsToId()
    {
        return $this->categoryId;
    }
}
