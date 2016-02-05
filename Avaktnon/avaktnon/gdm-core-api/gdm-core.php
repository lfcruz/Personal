<?php 
session_start();
include_once "../../lib/requestClass.php";
    $request = new coreRequest($_GET, $_POST);
    $response = null;
    switch ($_SERVER["REQUEST_METHOD"]){
        case "POST":
            $response = $request->validRequest();
            if ($response['response']['rspCode'] == "0000"){
                header("HTTP/1.1 200 Completed");
                echo json_encode($response);
            }else{
                header("HTTP/1.1 205 Reset");
                echo json_encode($response);
            }
            break;
        default:
            header("HTTP/1.1 400 Bad Request");
            break;
    }
?>