<?php

require_once "../model/element.class.php";

/**
 * Class KernelXML
 */
class KernelXML
{
    private $definitions;
    private $xml;

    /**
     * @details Cria o namespace principal
     * KernelXML constructor.
     */
    public function __construct()
    {
        $xml = new \DOMDocument('1.0','UTF-8');
        $xml->formatOutput = true;
        $root = $xml->createElementNS('http://www.omg.org/spec/BPMN/20100524/MODEL','definitions');
        $xml->appendChild($root);
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:bpmndi','http://www.omg.org/spec/BPMN/20100524/DI');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:omgdc','http://www.omg.org/spec/DD/20100524/DC');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:omgdi','http://www.omg.org/spec/DD/20100524/DI');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $this->definitions = $root;
        $this->xml = $xml;
    }

    /**
     * @param $list
     */
    public function createXML($list)
    {
        /**
         * @details Collaboration
         */
        $BPMNDiagram = null;
        $collaboration = $this->createElement('collaboration');
        $idCollaboration = \Model\Element::generateID();
        $this->createAtribute($collaboration,'id',$idCollaboration);
        $this->definitions->appendChild($collaboration);

        /**
         * @details BPMN Diagram
         */
        $BPMNDiagram = $this->createElement('bpmndi:BPMNDiagram');
        $this->definitions->appendChild($BPMNDiagram);
        $this->createAtribute($BPMNDiagram, 'id',\Model\Element::generateID());

        /**
         * @details BPMN Plane
         */
        $BPMNPlane = $this->createElement('bpmndi:BPMNPlane');
        $BPMNDiagram->appendChild($BPMNPlane);
        $this->createAtribute($BPMNPlane,'id',\Model\Element::generateID());
        $this->createAtribute($BPMNPlane,'bpmnElement',$idCollaboration);

        /**
         * @details BPMN Participant
         */
        $participant = $this->createElement('participant');
        $this->createAtribute($participant,'name','Mudança de Ticket');

        $processID = \Model\Element::generateID();

        /**
         * @details Process junto com o extensionElements (Pool com seu respectivo ID e Nome)
         */
        $process = $this->createElement('process');
        $extElement = $this->createElement('extensionElements');
        $process->appendChild($extElement);
        $this->definitions->appendChild($process);
        $this->createAtribute($process, 'id',$processID);
        $this->createAtribute($process, 'name','Mudança de Estado');
        $idParticipant = \Model\Element::generateID();
        $this->createAtribute($participant,'id',$idParticipant);

        /**
         * @details Shape calculando a altura (height) da Pool
         */
        $BPMNShape1 = $this->shape($idParticipant);
        $BPMNBounds1 = $this->bounds(147,108,933,$this->calcHeight($list));
        $BPMNShape1->appendChild($BPMNBounds1);
        $BPMNPlane->appendChild($BPMNShape1);

        $idLabel = \Model\Element::generateID();

        /**
         * @details BPMN Label
         */
        $BPMNLabel1 = $this->createElement('bpmndi:BPMNLabel');
        $this->createAtribute($BPMNLabel1, 'labelStyle',$idLabel);
        $BPMB = $this->bounds(48,170,12,59);
        $BPMNLabel1->appendChild($BPMB);
        $BPMNShape1->appendChild($BPMNLabel1);
        $this->createAtribute($participant,'processRef',$processID);
        $collaboration->appendChild($participant);

        /**
         * @details laneSet
         */
        $laneSet = $this->createElement('laneSet');
        $idLaneSet = \Model\Element::generateID();
        $this->createAtribute($laneSet,'id',$idLaneSet);
        $process->appendChild($laneSet);

        /**
         * @details Foreach percorrendo a lista de objetos de elementos
         */
        foreach ($list as $index => $value){

            /**
             * @details criar as lane
             */
            $lane = $this->createElement('lane');
            $this->createAtribute($lane,'id',$value["pool"][0]->getId());
            $this->createAtribute($lane,'name',$value["pool"][0]->getName());
            $laneSet->appendChild($lane);

            $BPMNShape = $this->shape($value["pool"][0]->getId());

            /**
             * @details Shape Bounds
             */
            if(count($list)==1){
                $BPMNBounds = $this->bounds(177,108,903,$this->calcHeight($list));
                $BPMNShape->appendChild($BPMNBounds);
                $BPMNPlane->appendChild($BPMNShape);
            }else{
                if($index === 'interna'){
                    $BPMNBounds = $this->bounds(177,108,903,$value["pool"][0]->getLabel()->getHeight());
                }else{
                    $BPMNBounds = $this->bounds(177,$list["interna"]["pool"][0]->getLabel()->getHeight() + 108,903,$this->calcHeight($list) - $list["interna"]["pool"][0]->getLabel()->getHeight());
                }
                $BPMNShape->appendChild($BPMNBounds);
                $BPMNPlane->appendChild($BPMNShape);
            }

            /**
             * @details Create Element XML
             */
            $this->createElementsXML($value, $index, $process, $BPMNPlane, $lane);
        }

        /**
         * @details Cria as labels do padão
         */

        for($i = 0; $i < 2;$i++){
           $this->labelFont($idLabel,$BPMNDiagram);
            $idLabel = \Model\Element::generateID();
        }

        /**
         * @details Cria o arquivo XML do BPMN
         */
        $fileXML = $this->xml->saveXML();
        $nameFile = date('Y') . date('d') . time();
        $dir = __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR."$nameFile.xml";
        $fp = \fopen($dir, "w") or die("Unable to open file!");
        if(\fwrite($fp, $fileXML)){
            \fclose($fp);
            return $nameFile;
        }else{
            \fclose($fp);
            http_response_code(500);
            json_encode(['msg'=>'Ocorreu um erro ao gerar o arquivo']);
        }
    }

    /**
     * @param $idLabel
     * @param $BPMNDiagram
     */
    public function labelFont($idLabel, &$BPMNDiagram)
    {
        $BPMNLabel = $this->createElement('bpmndi:BPMNLabelStyle');
        $BPMNDiagram->appendChild($BPMNLabel);
        $this->createAtribute($BPMNLabel, 'id',$idLabel);
        $omgdcFont = $this->createElement('omgdc:Font');
        $BPMNLabel->appendChild($omgdcFont);
        $this->createAtribute($omgdcFont, 'name','Arial');
        $this->createAtribute($omgdcFont, 'size','11');
        $this->createAtribute($omgdcFont, 'isBold','false');
        $this->createAtribute($omgdcFont, 'isItalic','false');
        $this->createAtribute($omgdcFont, 'isUnderline','false');
        $this->createAtribute($omgdcFont, 'isStrikeThrough','false');
    }

    /**
     * @param $res
     * @param $index
     * @param $process
     * @param $BPMNPlane
     */
    public function createElementsXML($res, $index, &$process, &$BPMNPlane, &$lane)
    {
        $sequenceFlow = 0;
        foreach ($res as $key => $value){
            if($key !== 'pool' and $key !=='sequenceFlow'){
                foreach ($value as $i => $vl){
                    $element = $this->createElement($vl->getTagName());
                    $this->createAtribute($element,'id',$vl->getId());
                    $this->createAtribute($element,'name',$vl->getName());

                    /**
                     * @details Cria os relacionamentos entre os elementos préviamente criados
                     */
                    if(isset($res['sequenceFlow'])){
                        foreach ($res['sequenceFlow'] as $x=>$flow){
                            if(($flow["sourceRef"]["id"] === $vl->getId() || $flow["targetRef"]["id"] === $vl->getId()) && !$flow["isCreated"] ){
                                $res['sequenceFlow'][$x]["isCreated"] = true;
                                $flow["isCreated"] = true;
                                /**
                                 * @details Cria todos os eleemntos e seus respecitvos atributos para fazer o relacionamento
                                 */
                                $sequenceFlow = $this->createElement('sequenceFlow');
                                $this->createAtribute($sequenceFlow,'id',$flow["id"]);
                                $this->createAtribute($sequenceFlow,'sourceRef',$flow["sourceRef"]["id"]);
                                $this->createAtribute($sequenceFlow,'targetRef',$flow["targetRef"]["id"]);
                                $incoming = $this->createElement('incoming',$flow["id"]);
                                $edge = $this->edge($flow["id"]);

                                $waypoint = $this->createElement('omgdi:waypoint');
                                $this->createAtribute($waypoint,'x',$flow["sourceRef"]["x"]);
                                $this->createAtribute($waypoint,'y',$flow["sourceRef"]["y"]);

                                $waypointMeio = $this->createElement('omgdi:waypoint');
                                $this->createAtribute($waypointMeio,'x',$flow["sourceRef"]["x"]);
                                $this->createAtribute($waypointMeio,'y',$flow["targetRef"]["y"]);

                                $waypoint2 = $this->createElement('omgdi:waypoint');
                                $this->createAtribute($waypoint2,'x',$flow["targetRef"]["x"]);
                                $this->createAtribute($waypoint2,'y',$flow["targetRef"]["y"]);
                                $BPMNLabel1 = $this->createElement('bpmndi:BPMNLabel');

                                /**
                                 * @details Medida Bounds (Padrão)
                                 */
                                $BPMB = $this->bounds(366,184,0,12);
                                $BPMNLabel1->appendChild($BPMB);

                                /**
                                 * @details Adc elementos no XML
                                 */
                                $edge->appendChild($waypoint);
                                $edge->appendChild($waypointMeio);
                                $edge->appendChild($waypoint2);
                                $edge->appendChild($BPMNLabel1);
                                $BPMNPlane->appendChild($edge);
                                $element->appendChild($incoming);
                                $process->appendChild($sequenceFlow);
                            }
                        }
                    }

                    $process->appendChild($element);
                    $BPMNShape = $this->shape($vl->getId());
                    $BPMNBounds = $this->bounds($vl->getLabel()->getX(),$vl->getLabel()->getY(), $vl->getLabel()->getWidth(),$vl->getLabel()->getHeight());
                    $BPMNShape->appendChild($BPMNBounds);
                    $BPMNPlane->appendChild($BPMNShape);
                    $flowNodeRed = $this->createElement('flowNodeRef', $vl->getId());
                    $lane->appendChild($flowNodeRed);
                }
            }
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edge($id)
    {
        $edge = $this->createElement('bpmndi:BPMNEdge');
        $this->createAtribute($edge, 'id',$id.'_di');
        $this->createAtribute($edge, 'bpmnElement',$id);
        return $edge;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function shape($id)
    {
        $BPMNShape1 = $this->createElement('bpmndi:BPMNShape');
        $this->createAtribute($BPMNShape1, 'id',$id.'_gui');
        $this->createAtribute($BPMNShape1, 'bpmnElement',$id);
        $this->createAtribute($BPMNShape1, 'isHorizontal','true');
        return $BPMNShape1;
    }

    public function calcHeight($list)
    {
        $result = 0;
        foreach ($list as $index=>$value)
        {
            $result += $value["pool"][0]->getLabel()->getHeight();
        }
        return $result;
    }

    /**
     * @param $x
     * @param $y
     * @param $w
     * @param $h
     * @return mixed
     */
    public function bounds($x, $y, $w, $h)
    {
        $BPMB = $this->createElement('omgdc:Bounds');
        $this->createAtribute($BPMB,'x',$x);
        $this->createAtribute($BPMB,'y',$y);
        $this->createAtribute($BPMB,'width',$w);
        $this->createAtribute($BPMB,'height',$h);
        return $BPMB;
    }

    /**
     * @details Cria um atributo de acordo com o elemento, nome e valor, já dando um append no elemento
     * @param $element
     * @param $name
     * @param string $value
     */
    public function createAtribute(&$element, $name, $value = "")
    {
        $atribute = $this->xml->createAttribute($name);
        $atribute->value = $value;
        $element->appendChild($atribute);
    }

    /**
     * @details Criar um elemento apartir do obj pai, nome e valor
     * @param $name
     * @param string $value
     * @return mixed
     */
    public function createElement($name, $value = "")
    {
        return $this->xml->createElement($name, $value);
    }
}
