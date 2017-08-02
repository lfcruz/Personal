<?php
Class httpClient {
    private $url;
    
    function __construct() {
        $this->url = "";
    }
    
    public function setURL($vurl){
        $this->url = $vurl;
    }
    
    public function httpRequest($vhttpMethod, $vhttpHeaders = null, $vhttpData = null) {
        $urlResponse = null;
        switch ($vhttpMethod) {
            case 'GET':
                $urlParams = array(CURLOPT_CUSTOMREQUEST => $vhttpMethod,
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOPT_FORBID_REUSE => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $vhttpHeaders);
                break;
            case 'POST':
                $urlParams = array(CURLOPT_CUSTOMREQUEST => $vhttpMethod,
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOPT_FORBID_REUSE => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $vhttpHeaders,
                    CURLOPT_POSTFIELDS => $vhttpData);
                break;
            case 'PUT':
                $urlParams = array(CURLOPT_CUSTOMREQUEST => $vhttpMethod,
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOPT_FORBID_REUSE => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $vhttpHeaders);
                break;
            case 'DELETE':
                $urlParams = array(CURLOPT_CUSTOMREQUEST => $vhttpMethod,
                    CURLOPT_FRESH_CONNECT => true,
                    CURLOPT_FORBID_REUSE => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $vhttpHeaders);
                break;
            default:
                break;
        }
        $urlResource = curl_init($this->url);
        if (!$urlResource) {
            $urlResponse = "<HTML><HEAD><TITLE></TITLE></HEAD><BODY></BODY></HTML>";
        }else {
            curl_setopt_array($urlResource, $urlParams);
            $urlResponse = curl_exec($urlResource);
            curl_close($urlResource);
        }
        return $urlResponse;    
    }
}