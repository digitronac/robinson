<?php
namespace Robinson\Frontend\Filter;

class Unaccent implements \Phalcon\Filter\UserFilterInterface
{

    /**
     * Filters a value
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter($string)
    {
        return str_replace(
            array('š', 'ć', 'č', 'ž', 'đ', 'Š', 'Ć', 'Ž', 'Đ'),
            array('s', 'c', 'c', 'z', 'd', 'S', 'C', 'Z', 'D'),
            $string
        );
    }
}
