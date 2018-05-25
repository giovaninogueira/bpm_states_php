<?php

namespace Model;

class Label
{
    private $x;
    private $y;
    private $width;
    private $height;

    public function __construct()
    {
    }

    public function setX($x)
    {
        $this->x = $x;
    }

    public function getX()
    {
        return $this->x;
    }

    public function setY($y)
    {
        $this->y  =$y;
    }

    public function getY()
    {
        return $this->y;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getHeight()
    {
        return $this->height;
    }
}