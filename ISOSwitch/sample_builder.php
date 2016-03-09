<?php
include_once('isoPackager.php');

$isoMsg = new isoPack();

//add data
$isoMsg->addMTI('0800');
$isoMsg->addData(7, date("mdHis"));
$isoMsg->addData(11, rand(1000, 999999));
$isoMsg->addData(70, '301');

//get iso string
print $isoMsg->getISO();
echo "\n\n";
var_dump($isoMsg->getData());
?>