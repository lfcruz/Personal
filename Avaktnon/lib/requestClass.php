<?php
include_once "customerClass.php";
class coreRequest {
    private $process;
    private $id;
    private $data;
    private $hashKey;
    
    function __construct($vGetArray, $vPostArray){
        $this->process = $vGetArray['process'];
        $this->id = $vGetArray['id'];
        $this->data = $vPostArray;
        $this->hashKey = "1s_2_H@rd_2_b3l13v3";
    }
    
    public function validRequest (){
        $valid = true;
        switch ($this->process){
            case "signin":
                break;
            case "login":
                break;
            default:
                $valid = false;
                break;
        }
        return $valid;
    }

    private function genSessionId (){
        $this->sessionid = date('YmdHis').rand(0,9999999999);
    }
    
    public function setHandset ($vHandset){
    }
    
    public function setPin ($vPin){
    }
    
    public function pinAssigment (){
    }
    
    public function pinValidation (){
    }
}

?>
