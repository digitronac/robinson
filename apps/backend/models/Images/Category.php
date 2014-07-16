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
        return (int) $this->categoryImageId;
    }
    
    /**
     * Returns path where images are saved.
     * 
     * @return string
     */
    public function getImagesPath()
    {
        if ($this->getDI()->has('config')) {
            return $this->getDI()->getShared('config')->application->categoryImagesPath;
        }
    }

    /**
     * Initializion method.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->setSource('category_images');
        $this->belongsTo('categoryId', 'Robinson\Backend\Models\Category', 'categoryId', array('alias' => 'category'));
        $this->setImageType(self::IMAGE_TYPE_CATEGORY);
    }
    
    /**
     * Sets categoryId.
     * 
     * @param int $categoryId categoryId
     * 
     * @return \Robinson\Backend\Models\Images\Category
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = (int) $categoryId;
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
