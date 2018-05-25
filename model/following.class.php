<?php

namespace Model;

require_once "element.class.php";

class Following extends Element
{
    private $sourceRef;
    private $targetRef;
    private $element;

    public function __construct()
    {
    }

    public function setElement($element)
    {
        $this->element = $element;
    }

    public function getElement()
    {
        return $this->element;
    }

    public function setSourceRef($sourceRef)
    {
        $this->sourceRef = $sourceRef;
    }

    public function getSourceref()
    {
        return $this->sourceRef;
    }

    public function setTargetTef($targetRef)
    {
        $this->targetRef = $targetRef;
    }

    public function getTargetRef()
    {
        return $this->targetRef;
    }
}