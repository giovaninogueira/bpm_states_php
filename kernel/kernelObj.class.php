<?php

namespace Kernel;
use Model\Element;
use Model\Following;
use Model\Label;

require_once "../model/element.class.php";
require_once "../model/label.class.php";
require_once "../model/following.class.php";

class KernelObj
{
    public $listObjElement = array();
    public $lastY = 0;

    public function __construct()
    {
    }

    /**
     * @details Cria os Objs do elemento Task
     * @param $list
     * @details Cria os obj do elemento task com suas respectivas posições (label)
     */
    public function createObjTask($list)
    {
        foreach ($list as $i => $res) {
            $firstTask = null;
            $id = Element::generateID();
            $lastObj = null;
            //verifica se a task é interna (primeira)
            if ($i === 'interna') {
                $firstTask = $this->createTask(
                    $id,
                    $res["begin"]["name"],
                    'task',
                    'task',
                    422,
                    165
                );
            } else {
                //verifica se a task é interna existe para fazer o calculo
                if (isset($this->listObjElement['interna'])) {
                    $firstTask = $this->createTask(
                        $id,
                        $res["begin"]["name"],
                        'task',
                        'task',
                        422,
                        $this->calcY() + 140
                    );
                } else {
                    //Caso não exista utiliza o X e Y padrão
                    $firstTask = $this->createTask(
                        $id,
                        $res["begin"]["name"],
                        'task',
                        'task',
                        422,
                        230
                    );
                }
            }
            //adc a primera task
            $this->listObjElement[$i]["firstTask"][] = $firstTask;

            foreach ($list[$i] as $index => $value) {
                if ($index !== "begin" && $value["next"] != $list[$i]["begin"]["id"]) {
                    if (!isset($this->listObjElement[$i]["task"])) {
                        if ($i === 'interna') {
                            $element = $firstTask = $this->createTask(
                                $id,
                                $value["name"],
                                'task',
                                'task',
                                800,
                                165
                            );
                        } else {
                            $element = $firstTask = $this->createTask(
                                $id,
                                $value["name"],
                                'task',
                                'task',
                                800,
                                $this->listObjElement[$i]["firstTask"][0]->getLabel()->getY()
                            );
                        }
                    } else {
                        $element = $firstTask = $this->createTask(
                            $id,
                            $value["name"],
                            'task',
                            'task',
                            800,
                            $this->listObjElement[$i]["firstTask"][0]->getLabel()->getY()
                        );
                        $label = $this->createLabel($lastObj);
                        $element->setLabel($label);
                    }
                    $id = Element::generateID();
                    $element->setId($id);
                    $lastObj = $element;
                    $this->listObjElement[$i]["task"][] = $element;
                }
            }
        }
    }

    /**
     * @details Cria o objeto elemento com os dados recebidos por parametro
     * @param $id
     * @param $name
     * @param $tagName
     * @param $type
     * @param $x
     * @param $y
     * @param $w
     * @param $h
     * @return Element
     */
    public function createTask($id, $name, $tagName, $type, $x, $y)
    {
        $element = new Element();
        $label = new Label();
        $element->setTagName($tagName);
        $element->setName($name);
        $element->setType($type);
        $label->setWidth(100);
        $label->setHeight(80);
        $label->setX($x);
        $label->sety($y);
        $element->setId($id);
        $element->setLabel($label);
        return $element;
    }


    /**
     * @param $result
     * @details Cria o obj start para o pool externo e interno, com suas respectivas posições
     */
    public function createStart($result)
    {
        foreach ($result as $index => $value) {
            $element = new Element();
            $label = new Label();
            $label->setHeight(36);
            $label->setWidth(36);
            $element->setName('Abertura do ticket');
            $element->setTagName('startEvent');
            $element->setType("startEvent");
            $id = Element::generateID();
            $element->setId($id);
            if ($index == "externa") {
                if (!isset($result["interna"])) {
                    if (isset($this->listObjElement['interna'])) {
                        $label->setY($this->calcY() + 600);
                        $label->setX(133);
                        $element->setLabel($label);
                    } else {
                        $label->setY(230);
                        $label->setX(250);
                        $element->setLabel($label);
                    }

                    $this->listObjElement[$index]["startEvent"][] = $element;
                }
            } else {
                $label->setX(274);
                $label->setY(187);
                $element->setLabel($label);
                $this->listObjElement[$index]["startEvent"][] = $element;
            }
        }
    }

    /**
     * @param $firstElement
     * @return Label
     * @details Cria o obj label a partir das posições do último elemento
     */
    private function createLabel($firstElement)
    {
        $label = new Label();
        $lastLabel = $firstElement->getLabel();
        $label->setX(800);
        $label->sety($lastLabel->getY() + 150);
        $label->setWidth(100);
        $label->setHeight(80);
        return $label;
    }

    /**
     * @details Pool
     * @param $list
     */
    public function createPool($list)
    {
        foreach ($list as $index => $value) {
            $element = new Element();
            $label = new Label();
            $element->setName($index);
            $element->setType('participant');
            $element->setTagName('participant');
            $element->setId(Element::generateID());
            $label->setWidth(740);
            $count = isset($this->listObjElement[$index]["task"]) ? count($this->listObjElement[$index]["task"]) : 1;
            /**
             * @details calcular height
             */
            $height = ($count * 150) + 100;
            $label->setHeight($height);

            if ($index === "interna") {
                $label->setX(147);
                $label->setY(108);
                $element->setLabel($label);
                $this->listObjElement[$index]["pool"][] = $element;
            } else {
                if (isset($this->listObjElement['interna'])) {
                    $label->setX(30);
                    $label->setY($this->calcY() + 500);
                    $element->setLabel($label);
                    $this->listObjElement[$index]["pool"][] = $element;
                } else {
                    $label->setX(30);
                    $label->setY(500);
                    $element->setLabel($label);
                    $this->listObjElement[$index]["pool"][] = $element;
                }
            }
        }
    }

    /**
     * @details Calcula a medida do Y de acordo com a quantidade de elementos da pilha interna
     * @return float|int
     */
    private function calcY()
    {
        $count = isset($this->listObjElement["interna"]["task"]) ? count($this->listObjElement["interna"]["task"]) : 1;
        $height = ($count * 150) + 100;
        return $height;
    }

    /**
     * @details calcula a medida X do flow
     * @param $obj
     * @return mixed
     */
    public function calcXFlow($obj)
    {
        return $obj->getLabel()->getWidth() + $obj->getLabel()->getX();
    }

    /**
     * @details calcula a medida Y do flow
     * @param $obj
     * @return float|int
     */
    public function calcYFlow($obj)
    {
        return ($obj->getLabel()->getHeight()/2) + $obj->getLabel()->getY();
    }

    /**
     * @details Add na itens na lista de sequenceFlow
     * @param $index
     * @param $source
     * @param $targetRef
     * @param null $x
     */
    private function addListenFlow($index, $source, $targetRef, $x = null)
    {
        $this->listObjElement[$index]["sequenceFlow"][] = [
            "id"=>Element::generateID(),
            "isCreated"=>false,
            "sourceRef"=>[
                "id"=>$source->getId(),
                "x"=>$this->calcXFlow($source),
                "y"=>$this->calcYFlow($source)
            ],
            "targetRef"=>[
                "id"=>$targetRef->getId(),
                "x"=>$targetRef->getLabel()->getX(),
                "y"=>$this->calcYFlow($targetRef)
            ]
        ];
        if($x){
            $lastKey = count($this->listObjElement[$index]["sequenceFlow"]) - 1;
            $this->listObjElement[$index]["sequenceFlow"][$lastKey]["targetRef"]["x"] = $this->calcXFlow($targetRef);
        }
    }

    /**
     * @details Gateway
     */
    public function createFollowing()
    {
        $gateway = null;
        foreach ($this->listObjElement as $index => $value) {
            /**
             * @details É e ligação principal entre o inicio do processo com a primeira task
             */
            if (isset($value["startEvent"])) {
                $this->addListenFlow($index, $value["startEvent"][0], $value["firstTask"][0]);
                $gateway = new Element();
                $gateway->setId(Element::generateID());
                $gateway->setType('exclusiveGateway');
                $gateway->setName('Mudar de estado para ?');
                $gateway->setTagName('exclusiveGateway');
                $label = new Label();
                $label->setWidth(50);
                $label->setHeight(50);
                $label->setX(632);
                $label->setY($this->calcYFlow($value["firstTask"][0]) - 25);
                $gateway->setLabel($label);
                $this->listObjElement[$index]["exclusiveGateway"][] = $gateway;
                $this->addListenFlow($index,$value["firstTask"][0],$gateway);
            }else if($gateway){
                $this->addListenFlow($index,$gateway,$value["firstTask"][0], true);
            }
            foreach ($value["task"] as $key => $elements){
                $this->addListenFlow($index,$gateway,$elements);
            }
        }
    }
}