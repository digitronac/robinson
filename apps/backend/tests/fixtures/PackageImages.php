<?php
// @codingStandardsIgnoreStart
namespace Phalcon\Test\Fixtures;
class PackageImages
{
    /**
     * Creates PackageImages fixtures.
     * 
     * @param array $records records
     * 
     * @return string
     */
    public static function get($records = null)
    {
        $template = "(%d, '%s', '%s', '%s', %d, %d, '%s')";
        for ($i = 1; $i <= 5; $i++)
        {
            $data[] = "($i, 'testpackageimage-{$i}', 'some cool title {$i}', '2014-01-01 0$i:00:00', 1, $i, 'jpg', 300, 200)";
        }
        return $data;
    }
}