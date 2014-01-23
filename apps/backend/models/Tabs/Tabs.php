<?php
namespace Robinson\Backend\Models\Tabs;
abstract class Tabs extends \Phalcon\Mvc\Model
{
    protected $title;
   
    protected $description;
    
    protected $type;
    
    protected $createdAt;
    
    protected $updatedAt;
    
    /**
     * Initialization method. Must be overriden.
     * 
     * @return void
     */
    abstract public function initialize();
    
    /**
     * Will be executed when object is created.
     * 
     * @return void
     */
    public function onConstruct()
    {
        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\Timestampable(array
        (
            'beforeValidationOnCreate' => array
            (
                'field' => 'createdAt',
                'format' => 'Y-m-d H:i:s',
            ),
            'beforeValidationOnUpdate' => array
            (
                'field' => 'updatedAt',
                'format' => 'Y-m-d H:i:s',
            ),
        )));
        
        $this->addBehavior(new \Phalcon\Mvc\Model\Behavior\Timestampable(array
        (
            'beforeValidationOnCreate' => array
            (
                'field' => 'updatedAt',
                'format' => 'Y-m-d H:i:s',
            ),
        )));
    }
    
    /**
     * Sets tab title.
     * 
     * @param string $title title
     *
     * @return \Robinson\Backend\Models\Tabs\Tabs
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this; 
    }
    
    /**
     * Gets tab title.
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Sets tab description.
     * 
     * @param string $description description
     * 
     * @return \Robinson\Backend\Models\Tabs\Tabs
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Gets tab description.
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Sets tab type.
     * 
     * @param int $type type
     * 
     * @return \Robinson\Backend\Models\Tabs\Tabs
     */
    public function setType($type)
    {
        $this->type = (int) $type;
        return $this;
    }
    
    /**
     * Gets tab type.
     * 
     * @return int
     */
    public function getType()
    {
        return (int) $this->type;
    }
}