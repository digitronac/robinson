<?php
namespace Robinson\Backend\Models;

class Cover
{
    private $image;

    private $text;

    private $price;

    public function __construct($data)
    {
        $this->image = $data->image;
        $this->text = $data->text;
        $this->price = $data->price;
    }

    public function getCoverData($property)
    {
        return $this->{$property};
    }
}
