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
        return $this->imageCategoryId;
    }
    
    public function save($data = null, $whiteList = null)
    {
        if(null === $this->sort)
        {
            $this->sort = (int) self::maximum(array('column' => 'sort')) + 1;
        }
        
        if(null === $this->createdAt)
        {
            $this->createdAt = date('Y-m-d H:i:s');
        }
        
        $this->file->moveTo($this->getDI()->getShared('config')->application->categoryImagesPath . '/' . $this->getFilename());
 
        return parent::save($data, $whiteList);
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
        
        //$imagine = new \Imagine\Imagick\Imagine();
       // $imagine->open($file)
         //   ->resize(new \Imagine\Image\Box($width, $height))
           // ->save($cropfile);
        $imagick = new \Imagick($file);
        $imagick->scaleimage($width, $height);
        $imagick->writeimage($cropfile);
        return '/img/category/' . $width . 'x' . $height . '_' . $this->getFilename();
        
    }
}