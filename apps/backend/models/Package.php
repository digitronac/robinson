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
     * Initialization.
     * 
     * @return void
     */
    public function initialize()
    {
        $this->setSource('packages');
        $this->belongsTo('destinationId', 'Robinson\Backend\Models\Destinations', 'destinationId', array
        (
            'alias' => 'destination', 
        ));
    }
    
    /**
     * Sets pdf.
     * 
     * @param string $pdf pdf path
     * 
     * @return \Robinson\Backend\Models\Package
     */
    protected function setPdf($pdf)
    {
        $this->pdf = $pdf;
        return $this;
    }
    
    /**
     * Called when new package is created.
     * 
     * @param array $data data
     * @param array $whitelist whitelist
     * 
     * @return void
     */
    public function create($data = null, $whitelist = null)
    {
        return $this->parentCreate();
    }
    
    /**
     * Event which is trigger before calling self::parentCreate.
     * 
     * @return void
     */
    public function beforeValidationOnCreate()
    {
        if (is_null($this->createdAt))
        {
            $this->createdAt = (new \DateTime('now', date_default_timezone_get()))->format('Y-m-d H:i:s');
        }
        
        if (is_null($this->updatedAt))
        {
            $this->updatedAt = (new \DateTime('now', date_default_timezone_get()))->format('Y-m-d H:i:s');
        }
        
        if ($this->uploadedPdf instanceof \Phalcon\Http\Request\File)
        {
            $this->setPdf($this->uploadedPdf->getName());
        }
    }
    
    /**
     * Moves file to appropriate folder.
     * 
     * @return void
     */
    public function afterCreate()
    {
        $this->uploadedPdf->moveTo($this->getDI()->getShared('config')->application->packagePdfPath);
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