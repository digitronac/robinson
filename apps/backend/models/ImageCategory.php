<?php
namespace Robinson\Backend\Models;
class ImageCategory extends \Phalcon\Mvc\Model
{
    protected $imageCategoryId;
    
    protected $categoryId;
    
    public function initialize()
    {
        $this->setSource('ImageCategory');
        $this->hasOne('categoryId', 'Robinson\Backend\Models\Category', 'categoryId');
    }
}