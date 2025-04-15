<?php
function callussd($baseurl,$number,$telco,$texto){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseurl);

switch($telco){

        case 2:
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('MSISDN: 1'.$number.'','Connection: TE','TE: trailers'));
        $response = curl_exec ($ch);
        break;
        case 8:
        $post='<?xml version="1.0"?><methodCall><methodName>push.ussdbyMsisdn</methodName><params><param><value><string>+1'.$number.'</string></value></param><param><value><struct><member><name>message</name><value><string>'.$texto.'</string></value></member></struct></value></param></params></methodCall>';
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: text/xml'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec ($ch);
        break;
        case 10:
        $post='<?xml version=\'1.0\' encoding=\'UTF-8\'?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body><ns1:Pr_externo_soap..ussd_push xmlns:ns1="urn:VivaWS"><origen>*150#</origen><destino>'.$number.'</destino><texto>'.$texto.'</texto><dialogo>0</dialogo></ns1:Pr_externo_soap..ussd_push></soapenv:Body></soapenv:Envelope>';

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=UTF-8','SOAPAction: "urn:VivaWS#ussd_push"'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, 'TPAGO:TP@G0v1v@!');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $response = curl_exec ($ch);
        break;
}
curl_close($ch);
return $response;
}


function geturl($number,$telco,$message){
    switch($telco){
        case 2:
        $port='808'.substr($number,-1);
        $baseurl="http://172.18.47.2:8080/fcgi-bin/wIQpush?dest=1".$number."&src=101&data=url&text=http%3a%2f%2f172.19.1.112%3a".$port."%2fmann-ussd-http-1.0.1%2fjsp%2findex10.html?texto=".$message."&sessiontype=finish";
        break;
        case 8:
        $baseurl="http://172.16.120.175:5678/cgi-bin/cellcube_push";
        break;
        case 10:
        $baseurl="http://192.168.100.60/supreme/index.php/pr_externo_soap/index/wsdl";
        break;
    }
return $baseurl;
}

function sentMessage($number, $telco, $message, $type){
    switch ($type){
        case 2:
            break;
        case 1:
            $baseurl=geturl($number,$telco,$message);
            //callussd($baseurl,$number,$telco,$message);
            break;
        default :
            break;
    }
    return true;
}
?>
