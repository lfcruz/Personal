<?php
include_once 'dbClass.php';
include_once 'configLoader.php';
include_once 'cryptClass.php';
class gdmCustomer {
    public $status;
    private $customerId;
    private $clientId;
    public $customerProfile;
    private $keysProfile = Array("name", "midname", "lastname", "email", "phone",
                                 "country", "state", "city", "address1", "zipcode",
                                 "income", "fixexpenses");
    private $customerSecure;
    private $keysSecure = Array("last_changed","iterativechain","securechain");
    public $customerAccounts;
    public $customerServices;
    private $dbConnector;
    private $conf;
    public $validStructure;
    
    
    function __construct($vCustomerId = null, $vCustomerStructure = null) {
        $this->status = true;
        $this->validStructure = false;
        $this->conf = new configLoader();
        $this->dbConnector = new dbRequest($this->conf->structure["dbConfig"]['dbType'],
                                           $this->conf->structure["dbConfig"]['dbIP'],
                                           $this->conf->structure["dbConfig"]['dbPort'],
                                           $this->conf->structure["dbConfig"]['dbName'],
                                           $this->conf->structure["dbConfig"]['dbUser'],
                                           $this->conf->structure["dbConfig"]['dbPassword']);
        if ($vCustomerId != null){
            $this->customerId = $vCustomerId;
            if ($this->customerExist()){
                $this->getCustomerRecord();
            }elseif ($vCustomerStructure != null){
                $this->customerProfile = $vCustomerStructure["profile"];
                $this->customerSecure = $vCustomerStructure["secure"];
                $this->validStructure = $this->validateProfile();
            }else {
                $this->status = false;
            }
        }
    }
    
    
    private function customerExist() {
        $this->dbConnector->setQuery("select client_id from client_profile where userid = $1 ", Array($this->customerId));
        return $this->dbConnector->execQry();
    }
    
    private function getCustomerRecord(){
        $temp = null;
        $this->dbConnector->setQuery("select client_id from client_profile where userid = $1 ", Array($this->customerId));
        $temp = $this->dbConnector->execQry();
        $this->clientId = $temp[client_id];
        $this->dbConnector->setQuery("select name, midname, lastname, email, phone, country, state, city, address1, zipcode, income, fixexpenses from client_profile where client_id = $1", Array($this->clientId));
        $this->customerProfile = $this->dbConnector->execQry();
        $this->dbConnector->setQuery("select last_changed, iterativechain, securechain, status from client_security where client_id = $1", Array($this->clientId));
        $this->customerSecure = $this->dbConnector->execQry();
    }
    
    public function validateSecure($vPassword){
        $result = false;
        $interval = date_diff($this->customerSecure['last_changed'], date('Y-m-d'));
        $days = $interval->format('%a');
        $cryptEngine = new cryptChain();
        $cryptEngine->charChain = $vPassword;
        $result = ($this->customerSecure['securechain'] == $cryptEngine->pwdHash($this->customerSecure['iterativechain'])) ? true : false;
        if($days > 55){
            $result = false;
            $this->expireSecure();
        }elseif($days >= 45 and $result){
            $result = null;
        }
        return $result;
    }
    
    private function validateProfile(){
        $result = false;
        
    }
    
    private function expireSecure(){
        $this->dbConnector->setQuery("update client_secure set status = $1 where client_id = $2", Array("E",$this->clientId));
        $this->dbConnector->execQry();
    }
 }
?>
