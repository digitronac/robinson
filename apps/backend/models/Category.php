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
    
    public $imageCategory;
    
    public function initialize()
    {
        $this->setSource('Category');
        $this->hasMany('categoryId', 'Robinson\Backend\Models\ImageCategory', 'categoryId');
    }
    
    public function getCategory($escapeHtml = true)
    {
        return $this->getDI()->getShared('escaper')->escapeHtml($this->category);
    }
    
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setStatus($status)
    {
        $this->status = (int) $status;
        return $this;
    }
    
    public function getStatus()
    {
        return (int) $this->status;
    }
    
    public function getCategoryId()
    {
        return $this->categoryId;
    }
    
    public function setCreatedAt(\DateTime $datetime)
    {
        $this->createdAt = $datetime->format('Y-m-d H:i:s');
        return $this;
    }
    
    public function setUpdatedAt(\DateTime $datetime)
    {
        $this->updatedAt = $datetime->format('Y-m-d H:i:s');
        return $this;
    }
    
    public function getCreatedAt($format = 'd.m.Y. H:i:s')
    {
        return (new \DateTime($this->createdAt, 'Europe/Belgrade'))->format($format);
    }
    
    public function getUpdatedAt($format = 'd.m.Y. H:i:s')
    {
        return (new \DateTime($this->updatedAt, 'Europe/Belgrade'))->format($format);
    }
    
    public function getUpdateUrl()
    {
        return $this->getDI()->get('url')->get(array('for' => 'admin-update', 
        'controller' => 'category', 'action' => 'update', 'id' => $this->getCategoryId()));
    }
    
    public static function getStatusMessages()
    {
        return self::$statusMessages;
    }
    
    public function getImages()
    {
        if(!$this->imageCategory)
        {
            $this->imageCategory = $this->getRelated('Robinson\Backend\Models\ImageCategory');
        }
        
        return $this->imageCategory;
    }
}