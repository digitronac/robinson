<?php
namespace Robinson\Backend\Filter;
class Watermark implements \Phalcon\Filter\UserFilterInterface
{
    /**
     * Imagick created with watermarked image.
     * @var \Imagick 
     */
    protected $watermark;
    
    /**
     * Construct.
     * 
     * @param \Imagick $watermark imagick instance of file which is used for watermark
     */
    public function __construct(\Imagick $watermark)
    {
        $this->watermark = $watermark;
    }
    /**
     * Applies watermark to given image.
     * 
     * @param \Imagick $imagickFile Imagick file to watermark
     * 
     * @return bool true on success
     */
    public function filter($imagickFile)
    {
        if (!$imagickFile instanceof \Imagick)
        {
            throw new \InvalidArgumentException('$imagickFile passed to Watermark filter must be of Imagick instance.');
        }
        
        $imagickFile->compositeimage($this->watermark, \Imagick::COMPOSITE_OVER, 0, 0);
        return $imagickFile->writeimage($imagickFile->getfilename());
    }

}