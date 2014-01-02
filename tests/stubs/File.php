<?php
namespace Robinson\Stub\Request;
class File extends \Phalcon\Http\Request\File
{
    public function __construct($file, $key = null)
    {
        
    }
    
    public function getName()
    {
        return 'testfile.png';
    }
}