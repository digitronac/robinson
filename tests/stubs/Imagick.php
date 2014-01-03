<?php
namespace Robinson\Stub;
class Imagick extends \Imagick
{
    public function __construct($files)
    {
        //parent::__construct($files);
    }
    
    public function writeimage($filename = null)
    {
        return true;
        //parent::writeimage($filename);
    }
    
    public function scaleimage($cols, $rows, $bestfit = false)
    {
        return true;
        //parent::scaleimage($cols, $rows, $bestfit);
    }
}