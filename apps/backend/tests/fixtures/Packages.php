<?php
namespace Phalcon\Test\Fixtures;
// @codingStandardsIgnoreStart
class Packages
{
    public static function get($records = null)
    {
        // packageId, package, description, price, pdf, pdf2, status, slug, createdAt, updatedAt, destinationId, type, special
        $template = "(%d, '%s', '%s', %d, '%s', '%s', %d, '%s', '%s', '%s', %d, %d, '%s')";
        
        for ($i = 1; $i <= 5; $i++)
        {
            $special = "''";
            if ($i === 1) {
                $special = '2014-06-11';
            }
            $data[] = "($i, 'package{$i}', 'description{$i}', 999, 'pdffile-1.pdf', 'pdffile-2.pdf', 0, 'fixture-category/fixture-destination-1/package{$i}', '2014-01-17 1{$i}:00:00', '2014-01-17 1{$i}:00:00', 1, 0, $special)";
        }
        
        return $data;
    }
}