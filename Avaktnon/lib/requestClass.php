<?php
include_once "customerClass.php";
include_once "constants.php";
include_once "dbClass.php";
include_once "configLoader.php";
class coreRequest {
    private $httpRequest;
    private $conf;
    
    function __construct($vRequest){
        $this->httpRequest = $vRequest;
        $this->conf = new configLoader();
    }
    
    public function process (){
        $httpResponse = $this->generateResponse(E_INTERNAL);
        switch ($this->httpRequest["method"]){
            case "POST":
                switch ($this->httpRequest[0]){
                    case "login":
                        $httpResponse = $this->handlerLogin();
                        break;
                    case "changepwd":
                        break;
                    case "signin":
                        $httpResponse = $this->handlerSignin();
                        break;
                    case "updateprofile":
                        $httpResponse = $this->handlerUpdateProfile();
                        break;
                    case "updateservices":
                        $httpResponse = $this->handlerUpdateServices();
                        break;
                    case "addaccounts":
                        $httpResponse = $this->handlerAddAccounts();
                        break;
                    default:
                        $httpResponse = $this->generateResponse(E_PROCESS);
                        break;
                }
                break;
            case "DELETE":
                switch ($this->httpRequest[0]){
                    case "deleteaccount":
                        $httpResponse = $this->handlerDeleteAccount();
                        break;
                    case "deleteservice":
                        $httpResponse = $this->handlerDeleteServices();
                        break;
                    default:
                        $httpResponse = $this->generateResponse(E_PROCESS);
                        break;
                }
                break;
            case "GET":
                switch ($this->httpRequest[0]){
                    case "retrieve":
                        $httpResponse = $this->handlerRetrieveProfile();
                        break;
                    default:
                        break;
                }
                break;
            default:
                $httpResponse = $this->generateResponse(E_METHOD);
                break;
        }
        return $httpResponse;
        
    }
    
    private function validateSession($vSessionId){
        $dbConnector = new dbRequest($this->conf->structure["dbConfig"]["dbType"], $this->conf->structure["dbConfig"]["dbIP"], $this->conf->structure["dbConfig"]["dbPort"], $this->conf->structure["dbConfig"]["dbName"], $this->conf->structure["dbConfig"]["dbUser"], $this->conf->structure["dbConfig"]["dbPassword"]);
        $dbConnector->setQuery("select  from active_sessions where sessionid = $1", $parameters);
    }//Depricated.
    
    private function generateResponse($vErrorCode){
        $dbConnector = new dbRequest($this->conf->structure["dbConfig"]["dbType"], $this->conf->structure["dbConfig"]["dbIP"], $this->conf->structure["dbConfig"]["dbPort"], $this->conf->structure["dbConfig"]["dbName"], $this->conf->structure["dbConfig"]["dbUser"], $this->conf->structure["dbConfig"]["dbPassword"]);
        $dbConnector->setQuery("select * from error_codes where error_code = $1", Array($vErrorCode));
        $responseStructure = Array("http_rsp_code" => null,
                                   "proc_rsp_code" => null);
        $responseStructure["proc_rsp_code"] = $dbConnector->execQry();
        switch (substr($vErrorCode, 0, 1)){
            case "0":
                $responseStructure["http_rsp_code"] = HTTP_OK;
                break;
            case "9":
                $responseStructure["http_rsp_code"] = HTTP_INVALID;
                break;
            case "8":
                $responseStructure["http_rsp_code"] = HTTP_UNAUTHORIZED;
                break;
            default:
                $responseStructure["http_rsp_code"] = HTTP_ERROR;
                break;
        }
        return $responseStructure;
    }//Done.
    
    private function handlerLogin(){
        $data = null;
        $customer = new gdmCustomer($this->httpRequest[1]);
        if ($customer->status){
            switch ($customer->validateSecure($this->httpRequest["body"]["securechain"])){
                case null:
                    $data = $this->generateResponse(W_ACCOUNT_EXPIRING);
                    break;
                case true:
                    $data = $this->generateResponse(PROC_OK);
                    break;
                default:
                    $data = $this->generateResponse(E_AUTH_FAILED);
                    break;
            }
        }else {
            $data = $this->generateResponse(E_AUTH_FAILED);
        }
        return $data;
    }//Done.

    private function handlerSignin(){
        $data = null;
        $customer = new gdmCustomer($this->httpRequest[1], $this->httpRequest["body"]);
        if (!$customer->status){
            if($customer->validStructure){
                if($customer->saveCustomerRecord()){
                    $data = $this->generateResponse(PROC_OK);
                }else {
                    $data = $this->generateResponse(E_INTERNAL);
                }
            }else {
                $data = $this->generateResponse(E_INVALID_DATA);
            }
        }else {
            $data = $this->generateResponse(E_ACCOUNT_EXIST); 
        }
        return $data;
        
    }//Done.
    
    private function handlerUpdateProfile(){
        $data = null;
        $customer = new gdmCustomer($this->httpRequest[1], $this->httpRequest["body"]);
        if($customer->status){
            if($customer->validStructure and array_key_exists('profile', $this->httpRequest["body"])){
                foreach ($this->httpRequest["body"]["profile"] as $profilekey => $profilekeydata){
                    if($customer->customerProfile[$profilekey] !== $profilekeydata){
                        $customer->customerProfile[$profilekey] = $profilekeydata;
                    }
                }
                if($customer->updateCustomerProfile()){
                    $data = $this->generateResponse(PROC_OK);
                }else {
                    $data = $this->generateResponse(E_INTERNAL);
                }
            }else {
                $data = $this->generateResponse(E_INVALID_DATA);
            }
        }else {
            $data = $this->generateResponse(E_INVALID_ACCOUNT); 
        }
        return $data;
    }//Done.
    
    private function handlerRetrieveProfile(){
        $data = null;
        $customer = new gdmCustomer($this->httpRequest[1]);
        if ($customer->status){
            if(!empty($customer->customerProfile)){
                $data = $this->generateResponse(PROC_OK);
                $data['proc_rsp_code']['profile'] = $customer->customerProfile;
                $data['proc_rsp_code']['accounts'] = $customer->customerAccounts;
                $data['proc_rsp_code']['services'] = $customer->customerServices;
            }else {
                $data = $this->generateResponse(E_INTERNAL);
            }
        }else {
            $data = $this->generateResponse(E_INVALID_ACCOUNT);
        }
        return $data;    
    }//Done.
 
}

?>
