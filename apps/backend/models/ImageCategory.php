<?php
namespace Robinson\Backend\Models;
class ImageCategory extends \Phalcon\Mvc\Model
{   
    protected $basePath;
    
    protected $imageCategoryId;
    
    protected $categoryId;
    
    protected $filename;
    
    protected $extension;
    
    protected $createdAt;
    
    protected $sort;
    
    protected $action;
    
    /**
     *
     * @var \SplFileInfo 
     */
    protected $file;
    
    public function initialize()
    {
        $this->setSource('ImageCategory');
        $this->belongsTo('categoryId', 'Robinson\Backend\Models\Category', 'categoryId');
        $this->basePath = $this->getDI()->getShared('config')->application->categoryImagesPath;
    }
    
    public function getSort()
    {
        return (int) $this->sort;
    }
    
    public function setSort($sort)
    {
        $this->sort = (int) $sort;
        return $this;
    }
    
    /**
     * Creates and persists model from uploaded file
     * @param \Phalcon\Http\Request\File $file
     * @param int $categoryId
     * @return \Robinson\Backend\Models\ImageCategory
     */
    public function createFromUploadedFile(\Phalcon\Http\Request\File $file, $categoryId)
    {
        $slugify = $this->makeSlugify();
        $this->filename = $slugify->slugify(pathinfo($file->getName(), PATHINFO_BASENAME));
        $this->extension = pathinfo($file->getName(), PATHINFO_EXTENSION);
        $this->categoryId = $categoryId;
        if(!$this->save())
        {
            throw new \ErrorException('Unable to save ImageCategory model.');
        }
        
        if(!$file->moveTo($this->basePath . '/' . $this->getRealFilename()))
        {
            throw new \ErrorException('Unable to move uploaded file to destination "' . $this->basePath . '/' . $this->getRealFilename() . '".');
        }
        
        return $this;
    }
    
    public function getRealFilename()
    {
        return $this->getImageCategoryId() . '-' . $this->filename . '.' . $this->extension;
    }
    
    public function getFilename()
    {
        return $this->filename;
    }
    
    public function getImageCategoryId()
    {
        return (int) $this->imageCategoryId;
    }
    
    public function save($data = null, $whiteList = null)
    {
        if(null === $this->sort)
        {
            $this->sort = (int) self::maximum(array('categoryId = ' . $this->categoryId , 'column' => 'sort')) + 1;
        }
        
        if(null === $this->createdAt)
        {
            $this->createdAt = date('Y-m-d H:i:s');
        }
        
        return parent::save($data, $whiteList);
    }
    
    public function delete()
    {
        if(is_file($this->basePath . '/' . $this->getRealFilename()))
        {
            unlink($this->basePath . '/' . $this->getRealFilename());
        }
        
        $dirIterator = new \DirectoryIterator($this->basePath);
        while($dirIterator->valid())
        {
            if($dirIterator->current()->isDir())
            {
                $crop = $this->basePath . '/' . $dirIterator->current()->getFilename() . '/' . $this->getRealFilename();
                
                if(is_file($crop))
                {
                    unlink($crop);
                }
            }
            
            $dirIterator->next();
        }
        
        return parent::delete();
    }
    
    public function setCategoryId($categoryId)
    {
        $this->categoryId = (int) $categoryId;
        return $this;
    }
    
    public function getCategoryId()
    {
        return (int) $this->categoryId;
    }
    
    /**
     * Method which does image resizing, dimensions are sorted by folders with $width x $height names
     * @param int $width
     * @param int $height
     * @return string public path to image
     */
    public function getResizedSrc($width = 300, $height = 0)
    {
        $cropDir = $this->basePath . '/' . $width . 'x' . $height;
        
        if(!is_dir($cropDir))
        {
            mkdir($cropDir, 0755);
        }
        
        $cropFile = $cropDir . '/' . $this->getRealFilename();
        
        $public = '/img/category/' . $width . 'x' . $height . '/' . $this->getRealFilename();
        
        if(is_file($cropFile))
        {
            return $public;
        }

        $imagick = $this->getDI()->get('Imagick', array($this->basePath . '/' . $this->getRealFilename()));
        $imagick->scaleimage($width, $height);
        $imagick->writeimage($cropFile);
        return $public;
        
    }
    
    /**
     * @return \Cocur\Slugify\Slugify
     */
    protected function makeSlugify()
    {
        return $this->getDI()->get('Cocur\Slugify\Slugify');
    }
}