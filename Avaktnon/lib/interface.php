<?php
error_reporting(-1);
session_start();

include 'addedFunctions.php';

if(!isset($_POST['name'])) {
    echo "Can not access this page without login in."; 
    exit(1); 
}
if(isset($_POST['submit'])) {
    if(($_POST['name'])=="" || ($_POST['password'])=="") {
        echo "Username and Password must be provided.";
    }
 }

$user = $_POST['name'] ;
$password = $_POST['password'];


/*if (!ldap_auth($user,$password,'bcmGroup')) {
    echo "<script type='text/javascript'>alert('Invalid Username or Password, or your credentials has no access.');window.location.href='index.php'</script>";
    exit(1);
}*/

//bcmLogin($user);
$_SESSION['username'] = $user;
$_SESSION['expiretime'] = date("H:i", strtotime('+1 minutes'));
header("Location: frontPannel.php");
?>
