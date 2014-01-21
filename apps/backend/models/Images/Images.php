<?php
namespace Robinson\Backend\Models\Images;
abstract class Images extends \Phalcon\Mvc\Model
{
    const IMAGE_TYPE_DESTINATION = 'destination';
    const IMAGE_TYPE_CATEGORY = 'category';
    const IMAGE_TYPE_PACKAGE = 'package';
    
    /**
     * Allowed types of model.
     * 
     * @var array 
     */
    protected static $allowedTypes = array
    (
        self::IMAGE_TYPE_CATEGORY,
        self::IMAGE_TYPE_DESTINATION,
        self::IMAGE_TYPE_PACKAGE,
    );
    
    protected $basePath;
    
    protected $filename;
    
    protected $extension;
    
    protected $createdAt;
    
    protected $sort;
    
    protected $uploadedFile;
    
    /**
     *
     * @var \Symfony\Component\Filesystem\Filesystem 
     */
    protected $filesystem;
    
    /**
     * File which will be uploaded. If not upload this property is null.
     * 
     * @var \Phalcon\Http\Request\File
     */
    protected $uploadFile;
    
    /**
     * Type of model, can be one of \Robinson\Backend\Models\Images\Images constants.
     * 
     * @var string 
     */
    protected $imageType;
    
    /**
     * Initializion method. Must be overriden to provide relations.
     * 
     * @return void
     */
    abstract public function initialize();
    
    /**
     * Returns path to images on filesystem. Should be different for every model.
     * 
     * @return string path to images on filesystem
     */
     abstract public function getImagesPath();
    
    /**
     * Get id.
     * 
     * @return int
     */
    abstract public function getImageId();
    
    /**
     * Get id from model to which this model belongs.
     * 
     * @return void
     */
    abstract public function getBelongsToId();
    
    /**
     * Listener for construct event, sets images path.
     * 
     * @return void
     */
    public function onConstruct()
    {
        $this->basePath = $this->getImagesPath();
        if (!$this->filesystem)
        {
            $this->filesystem = $this->getDI()->getShared('fs');
        }
    }
    
    /**
     * Gets sort order of image.
     * 
     * @return int
     */
    public function getSort()
    {
        return (int) $this->sort;
    }
    
    /**
     * Sets sort order of image.
     * 
     * @param int $sort sort order
     * 
     * @return \Robinson\Backend\Models\Images\Images
     */
    public function setSort($sort)
    {
        $this->sort = (int) $sort;
        return $this;
    }
    
    /**
     * Sets base path.
     * 
     * @param string $basePath location where images are stored
     * 
     * @return \Robinson\Backend\Models\Images\Images
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
    }
    
    /**
     * Sets models imageType. 
     * Must be one of Robinson\Backend\Models\Images\Images constants.
     * 
     * @param string $imageType one of Robinson\Backend\Models\Images\Images constants.
     * 
     * @return \Robinson\Backend\Models\Images\Images
     * 
     * @throws \Robinson\Backend\Models\Images\Exception
     */
    public function setImageType($imageType)
    {
        if (!in_array($imageType, self::$allowedTypes))
        {
            throw new \Robinson\Backend\Models\Images\Exception(
                'imageType must be one of Robinson\Backend\Models\Images\Images.');
        }
        $this->imageType = $imageType;
        return $this;
    }
    
    /**
     * Returns type of model.
     * 
     * @return string
     */
    public function getImageType()
    {
        return $this->imageType;
    }
    
    /**
     * Creates and persists model from uploaded file.
     * 
     * @param \Phalcon\Http\Request\File $file uploaded file
     * 
     * @return \Robinson\Backend\Models\Images\Images
     */
    public function createFromUploadedFile(\Phalcon\Http\Request\File $file)
    { 
        if (!$this->basePath)
        {
            throw new \Robinson\Backend\Models\Images\Exception('basePath is not set.');
        }

        $this->filename = $this->getDI()->getShared('tag')
            ->friendlyTitle(pathinfo($file->getName(), PATHINFO_FILENAME));
        $this->extension = pathinfo($file->getName(), PATHINFO_EXTENSION);
        $this->uploadedFile = $file;
      
        return $this;
    }
    
    /**
     * Gets "real" filename of image, filesystem filename.
     * 
     * @return string
     */
    public function getRealFilename()
    {
        return $this->getImageId() . '-' . $this->filename . '.' . $this->extension;
    }
    
    /**
     * Saves image and sets its sort order and createdAt if one is not set.
     * 
     * @param mixed $data      inherited
     * @param mixed $whiteList inherited
     * 
     * @throws \Robinson\Backend\Models\Images\Exception 
     * @return bool
     */
    public function save($data = null, $whiteList = null)
    {
        if (!$this->imageType)
        {
            throw new \Robinson\Backend\Models\Images\Exception(
                'imageType property must be set prior to calling save.');
        }

        if (null === $this->sort)
        {
            $this->sort = ((int) self::maximum(array($this->getImageType() . 'Id=' . $this->getBelongsToId(), 
            'column' => 'sort'))) + 1;
        }
        
        if (null === $this->createdAt)
        {
            $this->createdAt = date('Y-m-d H:i:s');
        }

        if (!$this->parentSave($data, $whiteList))
        {
            throw new \Robinson\Backend\Models\Images\Exception(sprintf('Unable to save %s image model.', 
                $this->imageType));
        }
        
        return true;
    }
    
    /**
     * Called after successful save.
     * 
     * @throws \Robinson\Backend\Models\Images\Images
     * @return void
     */
    public function afterSave()
    {
        // no image attached? pass...
        if (!$this->uploadedFile)
        {
           return; 
        }

        if (!$this->uploadedFile->moveTo($this->basePath . '/' . $this->getRealFilename()))
        {
            throw new \Robinson\Backend\Models\Images\Exception(
                sprintf('Unable to move uploaded file to destination "%s".', 
                    $this->basePath . '/' . $this->getRealFilename()));
        }
    }
    
    /**
     * Deletes image from db and filesystem.
     * 
     * @return bool
     * @throws \Robinson\Backend\Models\Images\Images if basePath is not set
     */
    public function delete()
    {
        if (!$this->basePath)
        {
            throw new \Robinson\Backend\Models\Images\Exception('basePath is not set.');
        }

        if ($this->filesystem->exists($this->basePath . '/' . $this->getRealFilename()))
        {
            $this->filesystem->remove($this->basePath . '/' . $this->getRealFilename());
        }
        
        $dirIterator = $this->getDI()->get('DirectoryIterator', array($this->basePath));
        
        while ($dirIterator->valid())
        {
            if ($dirIterator->current()->isDir())
            {
                $crop = $this->basePath . '/' . $dirIterator->current()->getFilename() . '/' . $this->getRealFilename();
                
                if ($this->filesystem->exists($crop))
                {
                    $this->filesystem->remove($crop);
                }
            }
            
            $dirIterator->next();
        }
        
        return $this->parentDelete();
    }
    
    /**
     * Overriden to provide easier PHPUnit mocking.
     * 
     * @return bolean
     */
    public function parentDelete()
    {
        return parent::delete();
    }
    
    /**
     * Overriden to provide easier PHPUnit mocking.
     * 
     * @param array $data      data
     * @param array $whiteList whiteList
     * 
     * @return bool
     */
    public function parentSave($data = null, $whiteList = null)
    {
        return parent::save($data, $whiteList);
    }
    
    /**
     * Method which does image resizing, dimensions are sorted by folders with $width x $height names.
     * 
     * @param int $width  px
     * @param int $height px
     * 
     * @throws \Robinson\Backend\Models\Images\Exception
     * @return string public path to image
     */
    public function getResizedSrc($width = 300, $height = 0)
    {
        if (!$this->imageType)
        {
            throw new \Robinson\Backend\Models\Images\Exception(
                'imageType must be set prior to calling getResizedSrc()');
        }
        
        $cropDir = $this->basePath . '/' . $width . 'x' . $height;
        if (!$this->filesystem->exists($cropDir))
        {
            $this->filesystem->mkdir($cropDir);
        }
        
        $cropFile = $cropDir . '/' . $this->getRealFilename();
        
        $public = '/img/' . $this->imageType . '/' . $width . 'x' . $height . '/' . $this->getRealFilename();
        
        if ($this->filesystem->exists($cropFile))
        {
            return $public;
        }

        $imagick = $this->getDI()->get('Imagick', array($this->basePath . '/' . $this->getRealFilename()));
        $imagick->scaleimage($width, $height);
        $imagick->writeimage($cropFile);
        return $public;
        
    }
}