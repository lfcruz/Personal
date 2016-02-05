<?php
// Defining Variables ---------------------------------------------------------
    global $configStructure;
    $stringfile = "";
 
// Configuration file validation ----------------------------------------------
    if(file_exists('../cfg/config.json')){
        $stringfile = file_get_contents('');
        $configStructure = json_decode($stringfile,true);
    } else {
        $configStructure = array();
    }
    
        /* Configuration file structure
         * BCMdbIp
         * BCMdbPort
         * BCMdbName
         * BCMdbUser
         * BCMdbPassword
         * BCMQueue
         */
?>
