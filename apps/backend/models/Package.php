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
    
    protected $destinationId;
    
    /**
     *
     * @var \Phalcon\Http\Request\File  
     */
    protected $uploadedPdf;
    
    /**
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;
    
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
     * Sets fs service on construct.
     * 
     * @return void
     */
    public function onConstruct()
    {
        if (!$this->filesystem)
        {
            $this->filesystem = $this->getDI()->getShared('fs');
        }
    }
    
    public function getPackageId()
    {
        return (int) $this->packageId;
    }

    /**
     * Sets package name.
     * 
     * @param string $package package name
     * 
     * @return \Robinson\Backend\Models\Package
     */
    public function setPackage($package)
    {
        $this->package = $package;
        return $this;
    }
    
    /**
     * Gets package name.
     * 
     * @return string package name
     */
    public function getPackage()
    {
        return $this->package;
    }
    
    /**
     * Sets package description.
     * 
     * @param string $description description
     * 
     * @return \Robinson\Backend\Models\Package
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Gets package description.
     * 
     * @return string package description
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Sets tabs content.
     * 
     * @param string $tabs tabs
     * 
     * @return \Robinson\Backend\Models\Package
     */
    public function setTabs($tabs)
    {
        $this->tabs = $tabs;
        return $this;
    }
    
    /**
     * Gets tabs.
     * 
     * @todo split tabs in array and use "-----" delimiter
     * 
     * @return type
     */
    public function getTabs()
    {
        return $this->tabs;
    }
    
    /**
     * Sets package starting price.
     * 
     * @param int $price package starting price
     * 
     * @return \Robinson\Backend\Models\Package
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }
    
    /**
     * Gets package lowest price.
     * 
     * @return int price lowest package price
     */
    public function getPrice()
    {
        return $this->price;
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
     * Gets pdf base file name.
     * 
     * @return string pdf's base file name
     */
    public function getPdf()
    {
        return $this->pdf;
    }
    
    /**
     * Sets package status.
     * 
     * @param int $status status
     * 
     * @return \Robinson\Backend\Models\Package
     */
    public function setStatus($status)
    {
        $this->status = (int) $status;
        return $this;
    }

    /**
     * Sets uploaded pdf file.
     * 
     * @param \Phalcon\Http\Request\File $pdf uploaded pdf
     * 
     * @return \Robinson\Backend\Models\Package
     */
    public function setUploadedPdf(\Phalcon\Http\Request\File $pdf)
    {
        $this->uploadedPdf = $pdf;
        return $this;
    }
    
    /**
     * Called when new package is created.
     * 
     * @param array $data      data
     * @param array $whitelist whitelist
     * 
     * @return void
     */
    public function create($data = null, $whitelist = null)
    {
        return $this->parentCreate($data, $whitelist);
    }
    
    /**
     * Sets package destination.
     * 
     * @param \Robinson\Backend\Models\Destinations $destination destination model
     * 
     * @return \Robinson\Backend\Models\Package
     */
    public function setDestination(\Robinson\Backend\Models\Destinations $destination)
    {
        $this->destination = $destination;
        return $this;
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
            $this->createdAt = (new \DateTime('now', new \DateTimeZone(date_default_timezone_get())))
                ->format('Y-m-d H:i:s');
        }
        
        if (is_null($this->updatedAt))
        {
            $this->updatedAt = (new \DateTime('now', new \DateTimeZone(date_default_timezone_get())))
                ->format('Y-m-d H:i:s');
        }
        
        if ($this->uploadedPdf instanceof \Phalcon\Http\Request\File)
        {
            $this->setPdf($this->uploadedPdf->getName());
        }
        
        if (is_null($this->status))
        {
            $this->status = self::STATUS_INVISIBLE;
        }
    }
    
    /**
     * Moves file to appropriate folder.
     * 
     * @return void
     */
    public function afterCreate()
    {
        $destinationFolder = $this->getDI()->getShared('config')->application->packagePdfPath;
        $destinationPackageFolder = $destinationFolder . '/' . $this->packageId;
        
        if (!$this->filesystem->exists($destinationPackageFolder))
        {
            $this->filesystem->mkdir($destinationPackageFolder);
        }
        
        if (!$this->uploadedPdf->moveTo($destinationPackageFolder))
        {
           throw new \Robinson\Backend\Models\Exception(sprintf('Unable to move pdf file "%s" to destination dir "%s"', 
               $this->uploadedPdf->getName(), $destinationFolder));
        }
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
        return parent::create($data, $whitelist);
    }
    
    
}