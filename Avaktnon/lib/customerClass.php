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
                                 "country", "state", "city", "address", "zipcode",
                                 "income", "fixexpenses");
    private $customerSecure;
    private $keysSecure = Array("last_changed","iterativechain","securechain");
    public $customerAccounts;
    public $customerServices;
    private $dbConnector;
    private $conf;
    public $validStructure;
    
    
    function __construct($vCustomerId = null, $vCustomerStructure = null) {
        $this->status = false;
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
            $this->status = $this->customerExist();
            if ($vCustomerStructure != null){
                $this->validStructure = $this->validateStructures($vCustomerStructure);
            }
            if ($this->status){
                $this->getCustomerRecord();
            }elseif ($this->validStructure){
                $this->customerProfile = $vCustomerStructure["profile"];
                $this->customerSecure = $vCustomerStructure["secure"];
            }
        }
    }
    
    // PRIVATE FUNCTIONS ******************************************************************
    private function customerExist() {
        $this->dbConnector->setQuery("select client_id from client_profile where userid = $1 ", Array($this->customerId));
        return !empty($this->dbConnector->execQry());
    }
    
    private function getCustomerRecord(){
        $temp = null;
        $this->dbConnector->setQuery("select client_id from client_profile where userid = $1 ", Array($this->customerId));
        $temp = $this->dbConnector->execQry();
        $this->clientId = $temp[0]['client_id'];
        $this->dbConnector->setQuery("select name, midname, lastname, email, phone, country, state, city, address, zipcode, income, fixexpenses from client_profile where client_id = $1", Array($this->clientId));
        $temp = $this->dbConnector->execQry();
        $this->customerProfile = $temp[0];
        $this->dbConnector->setQuery("select last_changed, iterativechain, securechain, status from client_security where client_id = $1", Array($this->clientId));
        $temp = $this->dbConnector->execQry();
        $this->customerSecure = $temp[0];
    }
    
    private function validateStructures($vNewStructure){
        $result = false;
        foreach ($vNewStructure as $structKey => $structType){
            switch ($structKey) {
                case 'profile':
                    $result = (trim($structType["name"]) !== null and trim($structType["name"]) !== "") ?  
                        (trim($structType["lastname"]) !== null and trim($structType["lastname"]) !== "") ? 
                            (filter_var($structType["email"], FILTER_VALIDATE_EMAIL) and trim($structType["email"]) !== "") ? 
                                (trim($structType["country"]) !== null and trim($structType["country"]) !== "") ? 
                                    (trim($structType["state"]) !== null and trim($structType["state"]) !== "") ? 
                                        (trim($structType["income"]) !== null and trim($structType["income"]) !== "" and intval($structType["income"]) !== 0) ?
                                            (trim($structType["fixexpenses"]) !== null and trim($structType["fixexpenses"]) !== "" and intval($structType["fixexpenses"]) !== 0) ? true : false
                                        : false 
                                    : false 
                                : false 
                            : false 
                        : false 
                    : false;
                    break;
                case 'secure':
                    $result = (trim($structType["securechain"]) !== null and trim($structType["securechain"]) !== "") ? true : false;
                    break;
                default:
                    $result = false;
                    break;
            }
            if(!$result){break;}
        }
        return $result;
    }
    
    private function expireSecure(){
        $this->dbConnector->setQuery("update client_secure set status = $1 where client_id = $2", Array("E",$this->clientId));
        $this->dbConnector->execQry();
    }
    
    
    
    //PUBLIC FUNCTIONS ********************************************************************
    public function validateSecure($vPassword){
        $result = false;
        $interval = date_diff(date_create($this->customerSecure['last_changed']), date_create(date('Y-m-d')));
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
    
    public function updateCustomerProfile(){
        $result = false;
        $this->dbConnector->setQuery("update client_profile set name = $1, midname = $2, lastname = $3, "
                                    . "email = $4, phone = $5, country = $6, state = $7, city = $8, "
                                    . "address = $9, zipcode = $10, income = $11, fixexpenses = $12 "
                                    . "where client_id = $13", 
                                    Array($this->customerProfile['name'], $this->customerProfile['midname'], 
                                        $this->customerProfile['lastname'], $this->customerProfile['email'], $this->customerProfile['phone'], 
                                        $this->customerProfile['country'], $this->customerProfile['state'], $this->customerProfile['city'], 
                                        $this->customerProfile['address'], $this->customerProfile['zipcode'], $this->customerProfile['income'], 
                                        $this->customerProfile['fixexpenses'], $this->clientId));
        $result = $this->dbConnector->execQry();
        return $result;
    }
    
    public function saveCustomerRecord(){
        $cryptEngine = new cryptChain();
        $result = false;
        $cryptEngine->charChain = $this->customerSecure['securechain'];
        $temp = $cryptEngine->pwdHash();
        $this->customerSecure['iterativechain'] = $temp['iterativechain'];
        $this->customerSecure['securechain'] = $temp['pwd_hash'];
        
        $this->dbConnector->setQuery("select nextval('seq_clientid')", Array());
        $result = $this->dbConnector->execQry();
        $this->clientId = $result[0]['nextval'];
            
        $this->dbConnector->setQuery("INSERT INTO client_profile(client_id, userid, name, midname, lastname, email, phone, country, state, city, 
                                      address, zipcode, income, fixexpenses) 
                                      VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14)",
                                      Array($this->clientId, $this->customerId, $this->customerProfile['name'], $this->customerProfile['midname'], 
                                               $this->customerProfile['lastname'], $this->customerProfile['email'], $this->customerProfile['phone'], 
                                               $this->customerProfile['country'], $this->customerProfile['state'], $this->customerProfile['city'], 
                                               $this->customerProfile['address'], $this->customerProfile['zipcode'], $this->customerProfile['income'], 
                                               $this->customerProfile['fixexpenses']));
        $result = $this->dbConnector->execQry();
            
        $this->dbConnector->setQuery("INSERT INTO client_security (client_id, last_changed, iterativechain, securechain, status)
                                      VALUES ($1, $2, $3, $4, $5)", 
                                      Array($this->clientId, date('Y-m-d'),$this->customerSecure["iterativechain"], $this->customerSecure["securechain"], "A"));
        $result = $this->dbConnector->execQry();
        return $result;
    }
    
    
    
    
 //End of the Class   
 }
?>
