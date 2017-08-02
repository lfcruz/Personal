<?php
include_once 'lib/httpClientClass.php';
$ipFindResource = new httpClient();
$domHTMLResult = new DOMDocument;
$listIPs = Array();

function getListIPs(){
    global $argv, $listIPs;

    if(file_exists($argv[1])){
        $file = fopen($argv[1], 'r');
        do {
            $itemIP = fgets($file);
            $itemIP = substr($itemIP, 0, strlen($itemIP)-1);
            $listIPs[$itemIP] = "";
        }while(!feof($file));
        fclose($file);
    }else {
        echo "Given filename do not exist!!!!!\n";
        exit(1);
    }
}

function parseHTML(){
    global $domHTMLResult, $listIPs;
    $location = null;
    foreach ($domHTMLResult->getElementsByTagName('span') as $keyElement){
        $location = $keyElement->nodeValue;
        break;
    }
    return $location;
}

//Main Function
if(key_exists(1, $argv)){
    getListIPs();
    foreach ($listIPs as $itemKey => $itemValue){
        $ipFindResource->setURL('http://www.ipgeek.net/'.$itemKey);
        $domHTMLResult->loadHTML($ipFindResource->httpRequest('GET', Array()));
        $listIPs[$itemKey] = parseHTML();
    }
    var_dump($listIPs);
    $file = fopen('resultFound.json', 'w');
    fwrite($file, json_encode($listIPs));
    fclose($file);
}else {
    echo "No IP files especified!!!!!\n";
}
