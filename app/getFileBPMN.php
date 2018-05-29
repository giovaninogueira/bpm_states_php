<?php

/**
 * @details Retornar o arquivo XML
 */
$nameFile = $_GET["name"];
header('Content-type: application/xml');
header('Access-Control-Allow-Origin: *');
$xml = file_get_contents("http://localhost/bpmn/data/".$nameFile.".xml");

if($xml)
    echo $xml;
else
    http_response_code(404);