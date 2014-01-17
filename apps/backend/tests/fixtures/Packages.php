<?php
namespace Phalcon\Test\Fixtures;
// @codeStandardsIgnoreStart
class Packages
{
    public static function get($records = null)
    {
        // packageId, package, description, tabs, price, pdf, status, createdAt, updatedAt, destinationId
        $template = "(%d, '%s', '%s', '%s', %d, '%s', %d, '%s', '%s', %d)";
        
        for ($i = 1; $i <= 5; $i++)
        {
            $data[] = "($i, 'package{$i}', 'test-file-{$i}.jpg', 'tabs-1-{$i} <br /> ----- tabs-2-{$i}', '9{$i}', 'pdffile-{$i}.pdf', 0, '2014-01-17 1{$i}:00:00', '2014-01-17 1{$i}:00:00', 1)";
        }
        
        return $data;
    }
}