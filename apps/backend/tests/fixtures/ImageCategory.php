<?php
namespace Phalcon\Test\Fixtures;
class ImageCategory
{
    /**
     * Creates ImageCategory fixtures.
     * 
     * @param array $records records
     * 
     * @return string
     */
    public static function get($records = null)
    {
        $template = "(%d, '%s', '%s', %d, %d, '%s')";
        for ($i = 1; $i <= 5; $i++)
        {
            $data[] = "($i, 'testfile$i', '2014-01-01 $i:00:00', 1, $i, 'jpg')";
        }
        return $data;
    }
}