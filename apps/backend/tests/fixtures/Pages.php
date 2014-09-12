<?php
namespace Phalcon\Test\Fixtures;
// @codingStandardsIgnoreStart
class Pages
{
    public static function get($records = null)
    {
        // pageId, title, body, slug, createdAt, updatedAt
        $template = "(%d, '%s', '%s', '%s', '%s', '%s')";

        for ($i = 1; $i <= 3; $i++)
        {
            $data[] = sprintf($template, $i, 'title' . $i, 'body' . $i, 'slug' . $i, "2014-09-12 11:40:00", '2014-09-12 11:40:00');
        }

        return $data;
    }
}