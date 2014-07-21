<?php
namespace Phalcon\Test\Fixtures;
class Categories
{
    /**
     * Gets categories fixtures.
     * 
     * @param array $records records
     * 
     * @return string
     */
    public static function get($records = null)
    {
        $template = "(%d, '%s', '%s', %d, '%s', '%s', '%s')";
        
        $data[] = "(1, 'fixture category', 'description test fixture category', 1, 'fixture-category',
            '2014-01-01 12:00:00', '2014-01-01 12:00:00')";
        return $data;
    }
}