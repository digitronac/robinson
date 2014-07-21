<?php
namespace Robinson\Frontend\Model\Tags;

abstract class Tag extends \Phalcon\Mvc\Model
{
    protected $tag;
    
    protected $type;
    
    protected $createdAt;
    
    /**
     * Initialization method.
     * 
     * @return void
     */
    abstract public function initialize();
    

    /**
     * Resolves tag type to tag title from configuration.
     *
     * @param int $type tag type
     *
     * @return string tag title
     */
    abstract public function resolveTypeToTagTitle($type);
}
