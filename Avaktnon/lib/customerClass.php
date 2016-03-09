<?php
include_once 'dbClass.php';
include_once 'configLoader.php';
class gdmCustomer {
    private $customerId;
    private $firstName;
    private $midName;
    private $lastName;
    private $gender;
    private $age;
    private $maritalStatus;
    private $educationLevel;
    private $proffession;
    private $creditScore;
    private $residencialStatus;
    private $householdSize;
    private $accounts;
    
    function __construct($vCustomerId, $vCustomerStructure = null) {
        $this->customerId = $vCustomerId;
        if ($vCustomerStructure){
            $this->firstName = $vCustomerStructure['firstname'];
            $this->midName = $vCustomerStructure['midname'];
            $this->lastName = $vCustomerStructure['lastname'];
            $this->gender = $vCustomerStructure['gender'];
            $this->age = $vCustomerStructure['age'];
            $this->maritalStatus = $vCustomerStructure['maritalstatus'];
            $this->educationLevel = $vCustomerStructure['educationlevel'];
            $this->proffession = $vCustomerStructure['proffession'];
            $this->creditScore = $vCustomerStructure['creditscore'];
            $this->residencialStatus = $vCustomerStructure['residencialstatus'];
            $this->householdSize = $vCustomerStructure['householdsize'];
        }elseif ($this->customerExist()) {
            $vCustomerRegister = new dbRequest($configStructure['dbType'], $configStructure['dbIP'], $configStructure['dbPort'], $configStructure['dbName'], $configStructure['dbUser'], $configStructure['dbPassword']);
        }
        $this->customerId = $vCustomerId;
    }
    
    public function addCustomer() {
        
    }
    
    private function customerExist() {
        
    }
    
}
?>
