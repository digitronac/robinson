<?php
namespace Phalcon\Test\Fixtures;
class Destinations
{
    /**
     * Gets destinations fixtures.
     * 
     * @param array $records records
     * 
     * @return string
     */
    public static function get($records = null)
    {
        $template = "(%d, '%s', '%s', %d, '%s', '%s', '%s', %d)";
        
        for ($i = 1; $i <= 5; $i++)
        {
            $data[] = "($i, 'fixture destination $i', 'description test fixture destination $i', 1, 
                'fixture-category/fixture-destination-$i', '2014-01-01 12:0{$i}:00', '2014-01-0{$i} 12:00:00', 1)";
        }
        return $data;
    }
}