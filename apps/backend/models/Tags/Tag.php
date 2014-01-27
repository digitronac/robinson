<?php
namespace Robinson\Backend\Models\Tags;
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
    abstract function initialize();
    
    /**
     * Method which is executed on construction.
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
        )));
    }
    
    /**
     * Sets tag title.
     * 
     * @param string $tag tag
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
     * Sets tag type.
     *
     * @param int $type type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = (int) $type;
        return $this;
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
    abstract function resolveTypeToTagTitle($type);
    
}