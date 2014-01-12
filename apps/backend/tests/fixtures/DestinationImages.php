<?php
namespace Phalcon\Test\Fixtures;
class DestinationImages
{
    /**
     * Gets destination images fixtures.
     * 
     * @param array $records records
     * 
     * @return string
     */
    public static function get($records = null)
    {
        // destinationImageId, filename, createdAt, destinationId, sort, extension
        $template = "(%d, '%s', '%s', %d, %d, '%s')";
        
        for ($i = 1; $i <= 5; $i++)
        {
            $data[] = "($i, 'destinationimage-{$i}', '2014-01-0{$i} 12:00:00', 3, $i, 'jpg')";
        }
        return $data;
    }
}