<?php
namespace Robinson\Backend\Models;

class Package extends \Phalcon\Mvc\Model
{
    const STATUS_INVISIBLE = 0;
    const STATUS_VISIBLE = 1;

    const TYPE_UNDEFINED = 0;
    const TYPE_APARTMENT = 1;
    const TYPE_HOTEL = 2;
    
    protected static $statusMessages = array
    (
        self::STATUS_INVISIBLE => 'nevidljiv',
        self::STATUS_VISIBLE => 'vidljiv',
    );

    protected static $types = array
    (
        self::TYPE_UNDEFINED => 'nedefinisan',
        self::TYPE_APARTMENT => 'apartman',
        self::TYPE_HOTEL => 'hotel',
    );
    
    protected $packageId;
    
    protected $package;
    
    protected $description;
    
    protected $price;
    
    protected $pdf;
    
    protected $status;
    
    protected $createdAt;
    
    protected $updatedAt;
    
    protected $destinationId;

    protected $pdf2;

    protected $type = self::TYPE_UNDEFINED;

    protected $special;
    
    /**
     *
     * @var \Phalcon\Http\Request\File  
     */
    protected $uploadedPdf;

    /**
     * @var \Phalcon\Http\Request\File
     */
    protected $uploadedPdf2;
    
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
        $this->belongsTo(
            'destinationId',
            'Robinson\Backend\Models\Destination',
            'destinationId',
            array(
            'alias' => 'destination',
            )
        );

        $this->hasMany(
            'packageId',
            'Robinson\Backend\Models\Images\Package',
            'packageId',
            array(
                'alias' => 'images',
            )
        );

        $this->hasMany(
            'packageId',
            'Robinson\Backend\Models\Tabs\Package',
            'packageId',
            array(
                'alias' => 'tabs',
            )
        );

        $this->hasMany(
            'packageId',
            'Robinson\Backend\Models\Tags\Package',
            'packageId',
            array(
                'alias' => 'tags',
            )
        );
        
        $this->addBehavior(
            new \Phalcon\Mvc\Model\Behavior\Timestampable(
                array(
                    'beforeValidationOnCreate' => array(
                        'field' => 'createdAt',
                        'format' => 'Y-m-d H:i:s',
                    ),
                )
            )
        );
            
        $this->addBehavior(
            new \Phalcon\Mvc\Model\Behavior\Timestampable(
                array(
                    'beforeValidationOnCreate' => array(
                        'field' => 'updatedAt',
                        'format' => 'Y-m-d H:i:s',
                    ),
                )
            )
        );
            
        $this->addBehavior(
            new \Phalcon\Mvc\Model\Behavior\Timestampable(
                array(
                    'beforeValidationOnUpdate' => array(
                        'field' => 'updatedAt',
                        'format' => 'Y-m-d H:i:s',
                    ),
                )
            )
        );
    }
    
    /**
     * Sets fs service on construct.
     * 
     * @return void
     */
    public function onConstruct()
    {
        if (!$this->filesystem) {
            $this->filesystem = $this->getDI()->getShared('fs');
        }
        
        $this->imagesContainer = new \SplObjectStorage();
    }
    
    /**
     * Gets packageId.
     * 
     * @return int
     */
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
     * Gets pdf base file name.
     *
     * @return string pdf's base file name
     */
    public function getPdf2()
    {
        return $this->pdf2;
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
     * Gets package status.
     * 
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->status;
    }

    /**
     * Special setter method.
     *
     * @param string $special special
     *
     * @return $this
     */
    public function setSpecial($special)
    {
        $this->special = $special;
        return $this;
    }

    /**
     * Special getter method.
     *
     * @return string
     */
    public function getSpecial()
    {
        return $this->special;
    }
    
    /**
     * Status helper method.
     * 
     * @return bool 
     */
    public function isNotVisible()
    {
        return ($this->getStatus() === self::STATUS_INVISIBLE);
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
     * Sets uploaded pdf2 file.
     *
     * @param \Phalcon\Http\Request\File $pdf uploaded pdf2
     *
     * @return $this
     */
    public function setUploadedPdf2(\Phalcon\Http\Request\File $pdf)
    {
        $this->uploadedPdf2 = $pdf;
        return $this;
    }
    
    /**
     * Called when new package is created.
     * 
     * @param array $data      data
     * @param array $whiteList whitelist
     * 
     * @return void
     */
    public function create($data = null, $whiteList = null)
    {
        return $this->parentCreate($data, $whiteList);
    }
    
    /**
     * Executed on update.
     * 
     * @param array $data      data
     * @param array $whiteList whitelist
     * 
     * @return bool
     */
    public function update($data = null, $whiteList = null)
    {
        return $this->parentUpdate($data, $whiteList);
    }
    
    /**
     * Sets package destination.
     * 
     * @param \Robinson\Backend\Models\Destination $destination destination model
     * 
     * @return \Robinson\Backend\Models\Package
     */
    public function setDestination(\Robinson\Backend\Models\Destination $destination)
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
        if ($this->uploadedPdf instanceof \Phalcon\Http\Request\File) {
            $this->setPdf($this->uploadedPdf->getName());
        }

        if ($this->uploadedPdf2 instanceof \Phalcon\Http\Request\File) {
            $this->pdf2 = $this->uploadedPdf2->getName();
        } else {
            $this->pdf2 = new \Phalcon\Db\RawValue('""');
        }
        
        if (is_null($this->status)) {
            $this->status = self::STATUS_INVISIBLE;
        }
    }
    
    /**
     * Event which is trigger before calling self::parentUpdate.
     * 
     * @return void
     */
    public function beforeValidationOnUpdate()
    {
        if ($this->uploadedPdf instanceof \Phalcon\Http\Request\File) {
            $this->setPdf($this->uploadedPdf->getName());
        }

        if ($this->uploadedPdf2 instanceof \Phalcon\Http\Request\File) {
            $this->pdf2 = $this->uploadedPdf2->getName();
        } else {
            $this->pdf2 = new \Phalcon\Db\RawValue('""');
        }
    }

    public function beforeValidation()
    {
        if (!$this->special) {
            $this->special = new \Phalcon\Db\RawValue('""');
        }
    }
    
    /**
     * Moves file to appropriate folder.
     * 
     * @return void
     */
    public function afterSave()
    {
        $destinationFolder = $this->getDI()->getShared('config')->application->packagePdfPath;
        $destinationPackageFolder = $destinationFolder . '/' . $this->packageId;
        
        if (!$this->filesystem->exists($destinationPackageFolder)) {
            $this->filesystem->mkdir($destinationPackageFolder);
        }

        if ($this->uploadedPdf) {
            if (!$this->uploadedPdf->moveTo($destinationPackageFolder . '/' . $this->uploadedPdf->getName())) {
                throw new \Robinson\Backend\Models\Exception(
                    sprintf(
                        'Unable to move pdf file "%s" to destination dir "%s"',
                        $this->uploadedPdf->getName(),
                        $destinationPackageFolder
                    )
                );
            }
        }

        if ($this->uploadedPdf2) {
            if (!$this->uploadedPdf2->moveTo($destinationPackageFolder . '/' . $this->uploadedPdf2->getName())) {
                throw new \Robinson\Backend\Models\Exception(
                    sprintf(
                        'Unable to move pdf2 file "%s" to destination dir "%s"',
                        $this->uploadedPdf2->getName(),
                        $destinationPackageFolder
                    )
                );
            }
        }
    }
    
    /**
     * Overriden create method.
     * 
     * @param array $data      data
     * @param array $whiteList data
     * 
     * @return bool
     */
    public function parentCreate($data = null, $whiteList = null)
    {
        return parent::create($data, $whiteList);
    }
    
    /**
     * Overriden update method.
     * 
     * @param array $data      data
     * @param array $whiteList data
     * 
     * @return bool
     */
    public function parentUpdate($data = null, $whiteList = null)
    {
        return parent::update($data, $whiteList);
    }
    
    /**
     * Retrieves destination to which package belongs to.
     * 
     * @return \Robinson\Backend\Models\Destination
     */
    public function getDestination()
    {
        return $this->getRelated('destination');
    }

    /**
     * Get package type.
     *
     * @return int
     */
    public function getType()
    {
        return (int) $this->type;
    }

    /**
     * Set package type.
     *
     * @param int $type package type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = (int) $type;
        return $this;
    }

    /**
     * Returns images sorted by sort.
     *
     * @return \Phalcon\Mvc\Model\Resultset\Simple
     */
    public function getImages()
    {
        return $this->getRelated(
            'images',
            array(
                'order' => 'sort ASC',
            )
        );
    }

    /**
     * Gets package tabs.
     *
     * @param array $params additional criteria
     *
     * @return \Phalcon\Mvc\Model\Resultset\Simple
     */
    public function getTabs(array $params = null)
    {
        return $this->getRelated('tabs', $params);
    }

    /**
     * Gets package tags.
     *
     * @param array $params additional criteria params
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getTags(array $params = null)
    {
        return $this->getRelated('tags', $params);
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
     * Returns human readable type text.
     *
     * @return string
     */
    public static function getTypeMessages()
    {
        return self::$types;
    }
    
    /**
     * Updates package tabs.
     * 
     * @param array $tabsData tabs data, received from form data
     * 
     * @return \Robinson\Backend\Models\Package fluent interface
     */
    public function updateTabs(array $tabsData)
    {
        $tabs = array();
        foreach ($tabsData as $type => $description) {
            $description = trim($description);
            $tab = $this->getTabs(
                array(
                    'type = :type:',
                    'bind' => array(
                        'type' => $type,
                    ),
                )
            )->getFirst();
            
               
            // no tab , no description -> skip
            if (!$tab && !$description) {
                continue;
            }

            // no description, tab exists -> delete tab
            if ($tab && !$description) {
                $tab->delete();
                continue;
            }

            // new tab -> create
            if (!$tab && $description) {
                $tab = new \Robinson\Backend\Models\Tabs\Package();
                $tab->setType($type)
                   ->setTitle($tab->resolveTypeToTitle());
            }

            $tab->setDescription($description);
            $tabs[] = $tab;
        }
        $this->tabs = $tabs;
        return $this;
    }

    /**
     * Updates package tags.
     *
     * @param array $tagsData tags, recieved from form
     *
     * @return $this
     */
    public function updateTags(array $tagsData)
    {
        foreach ($this->getTags() as $tag) {
            $tag->delete();
        }

        $tags = array();
        foreach ($tagsData as $type => $title) {
            $tag = new \Robinson\Backend\Models\Tags\Package();
            $tag->setTag($title)
                ->setType($type);
            $tags[] = $tag;
        }

        $this->tags = $tags;

        return $this;
    }
}
