<?php
namespace Robinson\Backend\Models\Images;
class Category extends \Robinson\Backend\Models\Images\Images
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
        return (int) $this->imageCategoryId;
    }
    
    /**
     * Returns path where images are saved.
     * 
     * @return string
     */
    public function getImagesPath()
    {
        if ($this->getDI()->has('config'))
        {
            return realpath($this->getDI()->getShared('config')->application->categoryImagesPath);
        }
    }

    /**
     * Initializion method.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->setSource('ImageCategory');
        $this->belongsTo('categoryId', 'Robinson\Backend\Models\Category', 'categoryId');
        $this->setImageType(self::IMAGE_TYPE_CATEGORY);
    }
    
    /**
     * Sets imageCategoryId.
     * 
     * @param int $imageCategoryId imageCategoryId
     * 
     * @return \Robinson\Backend\Models\Images\Category
     */
    public function setImageCategoryId($imageCategoryId)
    {
        $this->imageCategoryId = (int) $imageCategoryId;
        return $this;
    }

    /**
     * Id to which this model belongs.
     * 
     * @return int
     */
    public function getBelongsToId()
    {
        return $this->categoryId;
    }

}