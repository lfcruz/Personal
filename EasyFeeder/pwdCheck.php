<?php
include_once 'lib/cryptClass.php';
$crypt = new cryptChain();
var_dump($crypt->exEncode('popolita'));
?>