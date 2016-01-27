<?php
namespace Robinson\Frontend\Model;

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

    public function getImageSrc()
    {
        return $this->image;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getLink()
    {
        return $this->link;
    }
}
