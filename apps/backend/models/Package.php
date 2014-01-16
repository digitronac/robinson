<?php
namespace Robinson\Backend\Models;
class Package extends \Phalcon\Mvc\Model
{
    const STATUS_INVISIBLE = 0;
    const STATUS_VISIBLE = 1;
    
    protected static $statusMessages = array
    (
        self::STATUS_INVISIBLE => 'nevidljiv',
        self::STATUS_VISIBLE => 'vidljiv',
    );
    
    protected $packageId;
    
    protected $package;
    
    protected $description;
    
    protected $tabs;
    
    protected $price;
    
    protected $pdf;
    
    protected $status;
    
    protected $createdAt;
    
    protected $updatedAt;
    
    /**
     *
     * @var \Phalcon\Http\Request\File  
     */
    protected $uploadedPdf;
    
    /**
     * Called when new package is created.
     * 
     * @return void
     */
    public function create()
    {
        if (is_null($this->createdAt))
        {
            $this->createdAt = (new \DateTime('now', date_default_timezone_get()))->format('Y-m-d H:i:s');
        }
        
        if (is_null($this->updatedAt))
        {
            $this->updatedAt = (new \DateTime('now', date_default_timezone_get()))->format('Y-m-d H:i:s');
        }
        
        return $this->parentCreate();
    }
    
    /**
     * Event which is trigger after calling self::parentCreate.
     * 
     * @return void
     */
    public function afterCreate()
    {
        
    }
    
    /**
     * Overriden create method.
     * 
     * @param array $data      data
     * @param array $whitelist data
     * 
     * @return bool
     */
    public function parentCreate($data = null, $whitelist = null)
    {
        return parent::create($data, $whiteList);
    }
}