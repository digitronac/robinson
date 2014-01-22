<?php
namespace Robinson\Backend\Models;
class Category extends \Phalcon\Mvc\Model
{
    const STATUS_INVISIBLE = 0;
    const STATUS_VISIBLE = 1;
    
    protected static $statusMessages = array
    (
        self::STATUS_INVISIBLE => 'nevidljiv',
        self::STATUS_VISIBLE => 'vidljiv',
    );
    
    protected $categoryId;
    
    protected $category;
    
    protected $description;
    
    protected $status;
    
    protected $createdAt;
    
    protected $updatedAt;
    
    /**
     * Initializaton method.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->setSource('categories');
        $this->hasMany('categoryId', 'Robinson\Backend\Models\Images\Category', 'categoryId', array
        (
            'alias' => 'images',
        ));
        $this->hasMany('categoryId', 'Robinson\Backend\Models\Destinations', 'categoryId', array
        (
            'alias' => 'destinations',
        ));
    }
    
    /**
     * Getter method for category name.
     *  
     * @param bool $escapeHtml flag
     * 
     * @return string
     */
    public function getCategory($escapeHtml = true)
    {
        return $this->getDI()->getShared('escaper')->escapeHtml($this->category);
    }
    
    /**
     * Sets category name.
     * 
     * @param string $category category name
     * 
     * @return \Robinson\Backend\Models\Category
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }
    
    /**
     * Sets category description.
     * 
     * @param string $description description
     * 
     * @return \Robinson\Backend\Models\Category
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Gets category description.
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Sets category status.
     * 
     * @param int $status category status
     * 
     * @return \Robinson\Backend\Models\Category
     */
    public function setStatus($status)
    {
        $this->status = (int) $status;
        return $this;
    }
    
    /**
     * Gets category status.
     * 
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->status;
    }
    
    /**
     * Is category visible?
     * 
     * @return bool
     */
    public function isVisible()
    {
        return ($this->getStatus() === self::STATUS_VISIBLE);
    }
   
    /**
     * Gets categoryId.
     * 
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }
    
    /**
     * Sets category id.
     * 
     * @param int $id id
     * 
     * @return \Robinson\Backend\Models\Category
     */
    public function setCategoryId($id)
    {
        $this->categoryId = (int) $id;
        return $this;
    }
    
    /**
     * Sets datetime when category was created.
     * 
     * @param \DateTime $datetime datetime object whcih will be used for setting datetime
     * 
     * @return \Robinson\Backend\Models\Category
     */
    public function setCreatedAt(\DateTime $datetime)
    {
        $this->createdAt = $datetime->format('Y-m-d H:i:s');
        return $this;
    }
    
    /**
     * Sets datetime when category was last updated.
     * 
     * @param \DateTime $datetime datetime object whcih will be used for setting datetime
     * 
     * @return \Robinson\Backend\Models\Category
     */
    public function setUpdatedAt(\DateTime $datetime)
    {
        $this->updatedAt = $datetime->format('Y-m-d H:i:s');
        return $this;
    }
    
    /**
     * Gets createdAt category datetime.
     * 
     * @param string $format date format
     * 
     * @return string
     */
    public function getCreatedAt($format = 'd.m.Y. H:i:s')
    {
        return (new \DateTime($this->createdAt, new \DateTimeZone('Europe/Belgrade')))->format($format);
    }
    
    /**
     * Gets last updated category datetime.
     * 
     * @param string $format date format
     * 
     * @return string
     */
    public function getUpdatedAt($format = 'd.m.Y. H:i:s')
    {
        return (new \DateTime($this->updatedAt, new \DateTimeZone('Europe/Belgrade')))->format($format);
    }
    
    /**
     * Assembles and returns url for category update page.
     * 
     * @param bool $asArray flag, if set to true, url params will be returned as array
     * 
     * @return string|array
     */
    public function getUpdateUrl($asArray = false)
    {
        $fragments = array
        (
            'for' => 'admin-update', 
            'controller' => 'category', 
            'action' => 'update', 
            'id' => $this->getCategoryId()
        );
        if ($asArray)
        {
          return $fragments;  
        }
        
        return $this->getDI()->get('url')->get($fragments);
    }
    
    /**
     * Returns human readable status text.
     * 
     * @return string
     */
    public static function getStatusMessages()
    {
        return self::$statusMessages;
    }
    
    /**
     * Fetches related images.
     * 
     * @return \Phalcon\Mvc\Model\Resultset\Simple
     */
    public function getImages()
    {
        return $this->getRelated('images', array
        (
            'order' => 'sort ASC',
        ));
    }
    
    /**
     * Fetches related destinations.
     * 
     * @return \Phalcon\Mvc\Model\Resultset\Simple
     */
    public function getDestinations()
    {
        return $this->getRelated('destinations');
    }
}