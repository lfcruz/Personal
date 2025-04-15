<?php
include_once 'configLoader.php';

define ("MSG_ERROR","ERROR");
define ("MSG_WARNING","WARNING");
define ("MSG_INFO","INFO");
define ("REF_CONFIG_LOAD","LoadConfig");
define ("REF_SEQUENCE_LOAD","LoadSequence");
define ("REF_CONNECTIONS","StablishConnection");
define ("REF_SENSOR","ConnectionsSensor");
define ("REF_STATUS","AppStatusSensor");
define ("REF_LASTID","LastIDHandler");
define ("REF_SQL","GettingDBRecord");
define ("REF_GENERAL","Main");
define ("REF_SENDINGDATA","SendData");
define ("SQL_STRING","QueryName");

// Send Request ----------------------------------------------------------------
function do_post_request($url, $data, $optional_headers,$requestType)
{
  $urlResponse = null;
  switch ($requestType) {
    case 'GET': 
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $optional_headers);
            break;
    case 'POST':
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $optional_headers,
            CURLOPT_POSTFIELDS => $data);
            break;
    case 'PUT':
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $optional_headers);
            break;
    case 'DELETE':
        $urlParams = array(CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $optional_headers);
            break;
    default:
        break;
  }
  $urlResource = curl_init($url);
  if (!$urlResource) {
    echo("Problem stablishing resource.\n");
  } 
  else {
      curl_setopt_array($urlResource, $urlParams);
      $urlResponse = curl_exec($urlResource);
    if ($urlResponse === null) {
        echo("Problem reading data from URL.\n");
    }
    curl_close($urlResource);
  }
  return $urlResponse;
}

function sentToSocket($enviroment,$port,$msg){
  //create a socket to send message to core
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_connect($sock, $enviroment, $port);
    $sent = socket_write($sock, $msg->asXML(), strlen($msg->asXML()));

    //read response message from core
    $input = socket_read($sock, 1024);
    $dom = new DOMDocument;
    $dom->loadXML($input);
    if (!$dom) {
        $result = '9903';
    }
    else{
        $result = simplexml_import_dom($dom);
    }
    socket_close($sock);
    return $result;
}

function ldap_auth($user, $password, $group) {
        // Active Directory server
        $ldap_host = "172.22.1.4";

        // Active Directory DN
        $ldap_dn = "dc=gcs,dc=local";

        // Active Directory user group
        $ldap_user_group = $group; //"bcmGroup";

        // Active Directory manager group
        $ldap_manager_group = $group; //"bcmGroup";

        // Domain, for purposes of constructing $user
        $ldap_usr_dom = "@gcs.local";

        // connect to active directory
        $ldap = ldap_connect($ldap_host);

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION,3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS,0);

        // verify user and password
        if($bind = @ldap_bind($ldap, $user . $ldap_usr_dom, $password)) {
                // valid
                // check presence in groups
                $filter = "(sAMAccountName=" . $user . ")";
                $attr = array("memberof");
                $result = ldap_search($ldap, $ldap_dn, $filter, $attr) or exit("Unable to search LDAP server");
                $entries = ldap_get_entries($ldap, $result);
                ldap_unbind($ldap);

                // check groups
                foreach($entries[0]['memberof'] as $grps) {
                        // is manager, break loop
                        if (strpos($grps, $ldap_manager_group)) { $access = 2; break; }

                        // is user
                        if (strpos($grps, $ldap_user_group)) $access = 1;
                }

                if ($access != 0) {
                        // establish session variables
                        $_SESSION['user'] = $user;
                        $_SESSION['access'] = $access;
                        return true;
        return true;
                } else {
                        // user has no rights
                        return false;
                }

        } else {
                // invalid name or password
                return false;
        }
}

function bcmLogin($user) {
    $dbpgStructure = array ("dbIP" => "localhost",
        "dbPort" => "5432",
        "dbName" => "campaings",
        "dbUser" => "devuser",
        "dbPassword" => "L1nux2kkk",
        "dbQueryName" => "storeLogin",
        "dbQuery" => "insert into t_login (id,username,last_login) values (DEFAULT, $1, DEFAULT)",
        "dbQueryVariables" => array($user));
    dbpg_query($dbpgStructure);
}


function loadConfig() {
// Defining Variables ---------------------------------------------------------
    global $configStructure;
    $stringfile = "";
 
// Configuration file validation ----------------------------------------------
    if(file_exists(CONFIG_FILE)){
        $stringfile = file_get_contents(CONFIG_FILE);
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
    return $configStructure;
}

function email($srcAddresses,$dstAddresses,$subject,$message){
    return mail($dstAddresses, $subject, $message, 'From: '.$srcAddresses);
}

function writeLog($logType,$origReference,$logString) {
    switch ($configStructure['logLevel']) {
        case "DEBUG":
            error_log (date(DATE_RFC822)." ".$logType."[".$origReference."]: ".$logString, 3);
            break;
        case "ERROR":
            if ($logType == "ERROR") {
                error_log (date(DATE_RFC822)." ".$logType."[".$origReference."]: ".$logString, 3);
            }
            break;
        case "WARNG":
            if ($logType == "WARNING" or $logType == "ERROR") {
                error_log (date(DATE_RFC822)." ".$logType."[".$origReference."]: ".$logString, 3);
            }
            break;
        default:
            if ($logType == "INFO" or $logType == "ERROR") {
                error_log (date(DATE_RFC822)." ".$logType."[".$origReference."]: ".$logString, 3);
            }
            break;        
    }
   
   return;
}
?>