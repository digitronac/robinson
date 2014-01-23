<?php
namespace Phalcon\Test\Fixtures;
// @codingStandardsIgnoreStart
class DestinationTabs
{
    public static function get($records = null)
    {
        // destinationTabId, title, description, type, destinationId, createdAt, updatedAt
        $template = "(%d, '%s', '%s', %d, '%s', %d, '%s', '%s', %d)";
        
        for ($i = 1; $i <= 5; $i++)
        {
            $type = 2;
            if($i %2)
            {
                $type = 1;
            }
            
            $data[] = sprintf($template, $i, "title-{$i}", "description-{$i}", $type, 1, "2014-01-17 1{$i}:00:00", "2014-01-17 1{$i}:00:00");
            //$data[] = "($i, 'package{$i}', 'description{$i}', 999, 'pdffile-1.pdf', 0, '2014-01-17 1{$i}:00:00', '2014-01-17 1{$i}:00:00', 1)";
        }
        
        return $data;
    }
}