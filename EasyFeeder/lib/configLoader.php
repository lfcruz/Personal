<?php
class configLoader {
    public $structure;
    private $stringfile;
    private $status;
    
    function __construct(){
        if(file_exists('../../cfg/config.json')){
            $this->stringfile = file_get_contents('../../cfg/config.json');
            $this->structure = json_decode($this->stringfile,true);
            $this->status = true;
        } else {
            $this->structure = array();
            $this->status = false;
        }
        return $this->status;
    }
        /* Configuration file structure
         * BCMdbIp
         * BCMdbPort
         * BCMdbName
         * BCMdbUser
         * BCMdbPassword
         * BCMQueue
         */
}
    ?>
