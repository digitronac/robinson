<?php
namespace Phalcon\Test\Fixtures;
// @codingStandardsIgnoreStart
class PackageTags
{
    public static function get($records = null)
    {
        // packageTagId, tag, type, packageId, createdAt
        $template = "(%d, '%s', %d, %d, '%s')";
        
        for ($i = 1; $i <= 3; $i++)
        {
            $j = 0;
            foreach(\Phalcon\DI::getDefault()->getShared('config')->application->package->tags as $type => $tag)
            {
                $i = $i+$j;
                $data[] = sprintf($template, $i, "{$tag}", 1, $i, "2014-01-17 1{$i}:00:00");
                $j++;
            }
        }
        
        return $data;
    }
}