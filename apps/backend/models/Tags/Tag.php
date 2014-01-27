<?php
namespace Robinson\Backend\Models\Tags;
abstract class Tag extends \Phalcon\Mvc\Model
{
    protected $tag;
    
    protected $type;
    
    protected $packageId;
    
    protected $createdAt;
    
    /**
     * Initialization method.
     * 
     * @return void
     */
    abstract function initialize();
    
    /**
     * Method which is executed on creation.
     * 
     * @return void
     */
    public function onCreate()
    {
        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\Timestampable(array
        (
            'beforeValidationOnCreate' => array
            (
                'createdAt' => date('Y-m-d H:i:s'),
            ),
        )));
    }
    
    /**
     * Sets tag title.
     * 
     * @param string $tag
     * 
     * @return \Robinson\Backend\Models\Tags
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }
    
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
     * Resolves tag type to tag title from configuration.
     * 
     * @param int $type tag type
     * 
     * @return string tag title
     */
    abstract function resolveTypeToTagTitle($type);
    
}