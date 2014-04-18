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
     * Gets tag title.
     * 
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }


    /**
     * Gets tag type.
     *
     * @return int
     */
    public function getType()
    {
        return (int) $this->type;
    }
    
    /**
     * Resolves tag type to tag title from configuration.
     * 
     * @param int $type tag type
     * 
     * @return string tag title
     */
    abstract public function resolveTypeToTagTitle($type);
}
