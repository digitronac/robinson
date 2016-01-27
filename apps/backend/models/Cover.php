<?php
namespace Robinson\Backend\Models;

class Cover
{
    private $image;

    private $text;

    private $price;

    private $link;

    public function __construct($data)
    {
        $this->image = $data->image;
        $this->text = $data->text;
        $this->price = $data->price;
        $this->link = $data->link;
    }

    public function getCoverData($property)
    {
        return $this->{$property};
    }
}
