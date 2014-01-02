<?php
namespace Robinson\Backend\Models;
class ImageCategory extends \Phalcon\Mvc\Model
{   
    protected $imageCategoryId;
    
    protected $categoryId;
    
    protected $filename;
    
    protected $createdAt;
    
    protected $sort;
    
    protected $file;
    
    public function initialize()
    {
        $this->setSource('ImageCategory');
        $this->belongsTo('categoryId', 'Robinson\Backend\Models\Category', 'categoryId');
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
    
    public static function createFromUploadedFile(\Phalcon\Http\Request\File $file)
    {
        $self = new self();
        $slugify = new \Cocur\Slugify\Slugify();
        $suffix = uniqid();
        $fileinfo = new \SplFileInfo($file->getName());
        $self->setFilename($slugify->slugify($fileinfo->getFilename()) . '_' . $suffix . '.' . $fileinfo->getExtension());
        $self->file = $file;
        return $self;
    }
    
    protected function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }
    
    protected function getFilename()
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
            $this->sort = (int) self::maximum(array('categoryId=' . $this->categoryId , 'column' => 'sort')) + 1;
        }
        
        if(null === $this->createdAt)
        {
            $this->createdAt = date('Y-m-d H:i:s');
        }
        
        // doesnt exist ?
        if(!is_file($this->getDI()->getShared('config')->application->categoryImagesPath . '/' . $this->getFilename()))
        {
            $this->file->moveTo($this->getDI()->getShared('config')->application->categoryImagesPath . '/' . $this->getFilename());
        }
 
        return parent::save($data, $whiteList);
    }
    
    public function delete()
    {
        // exists ?
        if(is_file($this->getDI()->getShared('config')->application->categoryImagesPath . '/' . $this->getFilename()))
        {
            unlink($this->getDI()->getShared('config')->application->categoryImagesPath . '/' . $this->getFilename());
        }
        
        return parent::delete();
    }
    
    public function setCategoryId($categoryId)
    {
        $this->categoryId = (int) $categoryId;
        return $this;
    }
    
    public function getResizedSrc($width = 300, $height = 0)
    {
        $cropfile = APPLICATION_PATH . '/../public/img/category/' . $width . 'x' . $height . '_' . $this->getFilename();
        $file = APPLICATION_PATH . '/../public/img/category/' . $this->getFilename();
        
        if(is_file($cropfile))
        {
            return '/img/category/' . $width . 'x' . $height . '_' . $this->getFilename();
        }

        $imagick = $this->getDI()->get('Imagick', array($file));
        $imagick->scaleimage($width, $height);
        $imagick->writeimage($cropfile);
        return '/img/category/' . $width . 'x' . $height . '_' . $this->getFilename();
        
    }
}