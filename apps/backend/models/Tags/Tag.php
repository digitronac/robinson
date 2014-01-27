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
     * Resolves tag type to tag title from configuration.
     * 
     * @param int $type tag type
     * 
     * @return string tag title
     */
    abstract function resolveTypeToTagTitle($type);
    
}