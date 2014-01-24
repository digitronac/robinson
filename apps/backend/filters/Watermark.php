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
     * @param array $args filter arguments, should contain "destinationFile" and "imagickFile" keys
     * 
     * @return bool true on success
     */
    public function filter($args)
    {
        /* @var $imagickFile \Imagick */
        $imagickFile = $args['imagickFile'];
        $destination = $args['destinationFile'];
        $this->watermark->setimageopacity(0.2);
        $this->watermark->scaleimage($imagickFile->getimagewidth(), $imagickFile->getimageheight());
        //$centerwidth = (abs($imagickFile->getimagewidth() - $this->watermark->getimagewidth())) / 2;
        //$centerheight = (abs($imagickFile->getimageheight() - $this->watermark->getimageheight())) / 2;
        $centerwidth = $centerheight = 0;
        $imagickFile->compositeimage($this->watermark, \Imagick::COMPOSITE_OVER, $centerwidth, $centerheight);
        return $imagickFile->writeimage($destination);
    }

}