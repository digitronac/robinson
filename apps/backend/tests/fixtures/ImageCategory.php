<?php
namespace Phalcon\Test\Fixtures;
class ImageCategory
{
    public static function get($records = null)
    {
        $template = "(%d, '%s', '%s', %d, %d)";
        for($i = 1; $i <= 5; $i++)
        {
            $data[] = "($i, 'testfile$i.jpg', '2014-01-01 $i:00:00', 1, $i)";
        }
        return $data;
    }
}