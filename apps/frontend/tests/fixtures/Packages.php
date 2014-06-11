<?php
namespace Phalcon\Test\Fixtures;
// @codingStandardsIgnoreStart
class Packages
{
    public static function get($records = null)
    {
        // packageId, package, description, price, pdf, pdf2, status, createdAt, updatedAt, destinationId, type, date
        $template = "(%d, '%s', '%s', %d, '%s', '%s', %d, '%s', '%s', %d, '%s')";
        
        for ($i = 1; $i <= 5; $i++)
        {
            $date = null;
            $status = 1;
            if ($i == 3) {
                $status = 0;
                $date = '2014-06-11';
            }
            $data[] = "($i, 'package{$i}', 'description{$i}', 999, 'pdffile-1.pdf', 'pdffile-2.pdf', $status, '2014-01-17 1{$i}:00:00', '2014-01-17 1{$i}:00:00', 1, 0, '$date')";
        }

        return $data;
    }
}