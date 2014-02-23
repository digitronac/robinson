<?php
namespace Robinson\Frontend\Model\Images;
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
    
    protected $width;
    
    protected $height;
    
    /**
     *
     * @var \Symfony\Component\Filesystem\Filesystem 
     */
    protected $filesystem;
    
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
     * Sets models imageType.
     * Must be one of Robinson\Frontend\Model\Images\Images constants.
     *
     * @param string $imageType one of Robinson\Frontend\Model\Images\Images constants.
     *
     * @return \Robinson\Frontend\Model\Images\Images
     *
     * @throws \Robinson\Frontend\Model\Images\Exception
     */
    public function setImageType($imageType)
    {
        if (!in_array($imageType, self::$allowedTypes))
        {
            throw new \Robinson\Frontend\Model\Images\Exception(
                'imageType must be one of Robinson\Frontend\Model\Images\Images.');
        }
        $this->imageType = $imageType;
        return $this;
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
     * Gets width.
     * 
     * @return int
     */
    public function getWidth()
    {
        return (int) $this->width;
    }
    
    /**
     * Gets height.
     * 
     * @return int
     */
    public function getHeight()
    {
        return (int) $this->height;
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
     * Returns type of model.
     * 
     * @return string
     */
    public function getImageType()
    {
        return $this->imageType;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
        
        $dimensions = $this->sanitizeCropWidthAndHeight(array
        (
            'width' => $width,
            'height' => $height,
        ));
        
        $cropDir = $this->basePath . '/' . $dimensions['width'] . 'x' . $dimensions['height'];
        $cropFile = $cropDir . '/' . $this->getRealFilename();
        
        if (!$this->filesystem->exists($cropDir))
        {
            $this->filesystem->mkdir($cropDir);
        }
       
        if ($this->filesystem->exists($cropFile))
        {
            return $this->compileImgPath($dimensions['width'], $dimensions['height']);
        }

        $imagick = $this->getDI()->get('Imagick', array($this->basePath . '/' . $this->getRealFilename()));
        $imagick->thumbnailimage($dimensions['width'], $dimensions['height']);
        $imagick->writeimage($cropFile);
        
        // return before watermarking
        if (!$this->getDI()->getShared('config')->application->watermark->enable)
        {
            return $this->compileImgPath($dimensions['width'], $dimensions['height']);
        }
        
        $this->applyWatermark($cropFile);
        
        return $this->compileImgPath($dimensions['width'], $dimensions['height']);
    }
    
    /**
     * Sanitizes with and height if too large. 
     * 
     * @param array $dimensions dimensions array, should contain width and height
     * 
     * @return array
     */
    protected function sanitizeCropWidthAndHeight(array $dimensions)
    {
        if ($dimensions['width'] > $this->getWidth())
        {
            $dimensions['width'] = $this->getWidth();
        }
        
        if ($dimensions['height'] > $this->getHeight())
        {
            $dimensions['height'] = $this->getHeight();
        }
        
        return $dimensions;
    }
    
    /**
     * Applies watermark to image.
     * 
     * @param string $destination path where watermarked image will be saved
     * 
     * @return bool
     */
    protected function applyWatermark($destination)
    {
        /*$filter = new \Robinson\Backend\Filter\Watermark(new \Imagick($this->getDI()->getShared('config')
            ->application->watermark->watermark));
        $filter->filter(array
        (
            'imagickFile' => new \Imagick($destination),
            'destinationFile' => $destination,
        ));*/
        $filter =  $this->getDI()->get('watermark');
        $filter->filter(array
        (
            'imagickFile' => new \Imagick($destination),
            'destinationFile' => $destination,
        ));
    }
    
    /**
     * Compiles path to image (relative or absolute)
     * 
     * @param int    $width  width
     * @param int    $height height
     * @param string $type   relative|absolute
     * 
     * @return string
     */
    protected function compileImgPath($width, $height, $type = 'relative')
    {
        $baseImagePath = $this->imageType . '/' . $width . 'x' . $height . '/' . $this->getRealFilename();
        
        if ($type === 'relative')
        {
            return 'img/' . $baseImagePath;
        }
    }
}