<?php

namespace Model;

class Element
{

    private $id;
    private $name;
    private $type;
    private $tagName;
    private $label;

    public function __construct()
    {
    }

    /**
     * @details Gera um id unico
     * @param string $begin
     * @return string
     */
    public static function generateID($begin = "", $end = "")
    {
        mt_srand((double) microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = '-';
        $result = $begin . substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        $result .= ($end != "") ? "-".$end: "";
        return $result;
    }

    public function setTagName($tagName)
    {
        $this->tagName = $tagName;
    }

    public function getTagName()
    {
        return $this->tagName;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}