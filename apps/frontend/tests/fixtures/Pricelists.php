<?php
namespace Phalcon\Test\Fixtures;
// @codeCoverageIgnoreStart
class Pricelists
{
    public static function get($records = null)
    {
        // pricelistId, filename, createdAt
        $template = "(%d, '%s', '%s')";
        $data[] = sprintf($template, 1, 'fixturepdf.pdf', '2014-07-15 21:38:00');
        return $data;
    }
}
// @codeCoverageIgnoreEnd