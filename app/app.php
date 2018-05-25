<?php

header('Content-Type: application/xml');
    /**
     * @details Separar os states do tickets entre interno e exteno
     * @param $list
     * @return array
     */
    require_once "../kernel/kernelObj.class.php";
    require_once "../kernel/kernelXML.class.php";

    run();
    function run()
    {

        /*$begin[]=["id"=>4,"name"=>"Aberto","note_type"=>9];
        $list[]=["note_type"=>9,"name"=>"Aberto", "next"=>4,"begin"=>4];
        $list[]=["note_type"=>9,"name"=>"Em andamento", "next"=>10,"begin"=>4];
        $list[]=["note_type"=>9,"name"=>"Agendado", "next"=>11,"begin"=>4];*/

        $begin[]=["id"=>4,"name"=>"Aberto","note_type"=>10];
        $list[]=["note_type"=>10,"name"=>"Aberto", "next"=>4,"begin"=>4];
        $list[]=["note_type"=>10,"name"=>"Pendente Cliente", "next"=>10,"begin"=>4];
        $list[]=["note_type"=>10,"name"=>"Agendado", "next"=>11,"begin"=>4];

        stateWorkFlow($list, $begin);
    }
    function stateWorkFlow($list, $begin)
    {
        $noteType = array();
        foreach ($list as $index=>$value){
            if($value["note_type"] == 10){
                $noteType['externa'][] = $value;
            }else{
                $noteType['interna'][] = $value;
            }
        }

        foreach ($begin as $index=>$value){
            if($value["note_type"] == 10){
                $noteType['externa']["begin"] = $value;
            }else{
                $noteType['interna']["begin"] = $value;
            }
        }

        $kernel = new \Kernel\KernelObj();
        $kernel->createObjTask($noteType);
        $kernel->createStart($noteType);
        $kernel->createPool($noteType);
        $kernel->createFollowing();
        $xml = new KernelXML();
        $xml->createXML($kernel->listObjElement);
    }