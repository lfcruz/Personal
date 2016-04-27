<?php
class dbRequest {
    private $connectorStructure;
    private $queryStructure;
    
    function __construct($vDBType, $vDBIp, $vDBPort, $vDBName, $vDBUser, $vDBPassword){
        $this->connectorStructure['dbType'] = $vDBType;
        $this->connectorStructure['dbIP'] = $vDBIp;
        $this->connectorStructure['dbPort'] = $vDBPort;
        $this->connectorStructure['dbName'] = $vDBName;
        $this->connectorStructure['dbUser'] = $vDBUser;
        $this->connectorStructure['dbPassword'] = $vDBPassword;
        switch ($this->connectorStructure['dbType']) {
            case "ORA":
                $this->setQuery("select * from dual",Array());
                break;
            case "PGS":
                $this->setQuery("select datname from pg_database", Array());
                break;
            default:
                break;
        }
        $result = $this->execQry();
        return $result;
    }
    
    public function setQuery($query, $parameters){
        $this->queryStructure = Array("dbQuery" => $query,
                                      "qryParameters" => $parameters,
                                      "qryName" => "qry_".rand(100000,999999));
    }
    
    public function execQry(){
        $recordString = false;
        switch ($this->connectorStructure['dbType']) {
            case "ORA":
                $connectorString = '(DESCRIPTION = (CONNECT_TIMEOUT=5) (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST ='.$this->connectorStructure['dbIP'].')(PORT = '.$this->connectorStructure['dbPort'].')))(CONNECT_DATA=(SID= '.$this->connectorStructure['dbName'].')))';
                $dbConnector = oci_connect($this->connectorStructure['dbUser'], $this->connectorStructure['dbPassword'], $connectorString);
                if (!$dbConnector){
                    echo 'Failed connection.......';
                }else {
                    $oraQuery = oci_parse($dbConnector,$this->queryStructure['dbQuery']);
                }
                oci_execute($oraQuery);
                $rowsnum = oci_fetch_all($oraQuery,$recordString);
                if ($rowsnum == 0) {
                    $recordString = Array();
                }else {
                    if(!(substr($this->queryStructure['dbQuery'], 1, 6) == 'select')){
                        $oraQuery = oci_parse($dbConnector,'commit');
                        oci_execute($oraQuery);
                        $rowsnum = oci_fetch_all($oraQuery,$recordString);
                    }
                }
                oci_close($dbConnector);
                break;
            case "PGS":
                $connectorString = "host=".$this->connectorStructure['dbIP'].
                                   " port=".$this->connectorStructure['dbPort'].
                                   " dbname=".$this->connectorStructure['dbName'].
                                   " user=".$this->connectorStructure['dbUser'].
                                   " password=".$this->connectorStructure['dbPassword'];
                $dbConnector = pg_connect($connectorString);
                if (!$dbConnector){
                    echo 'Failed connection.......';
                }else {
                    pg_prepare($dbConnector,$this->queryStructure['qryName'],$this->queryStructure['dbQuery']);
                }
                $queryResult = pg_execute($dbConnector,$this->queryStructure['qryName'],$this->queryStructure['qryParameters']);
                if (!$queryResult){
                    $recordString = Array();
                }else {
                    $recordString = pg_fetch_all($queryResult);
                    if(!(substr($this->queryStructure['dbQuery'], 1, 6) == 'select')){
                        pg_prepare($dbConnector,'commit','commit');
                        pg_exec($dbConnector, 'commit');
                    }
                }
                pg_close($dbConnector);
                break;
            default:
                $recordString = false;
                break;
        }
        return $recordString;
    }
}
?>
