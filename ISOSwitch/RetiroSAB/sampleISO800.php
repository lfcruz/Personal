<?php
include_once 'isoPackager.php';
include_once 'socketServer.php';

$jak	= new isoPack();
$isoprint = "";
$isoServer = new socketProcessor("localhost", 18583, "C");

//add data
$jak->addMTI("0800");
$jak->addData(11, "15");
$jak->addData(41, "173664");
$jak->addData(42, "39457728837");
echo 'ISO: '. $iso. "\n";
echo 'MTI: '. $jak->getMTI(). "\n";
echo 'Bitmap: '. $jak->getBitmap(). "\n";
echo 'Data Element: '; print_r($jak->getData());echo "\n\n\n";

$data = $jak->getData();
$isoprint .= pack('H*', $jak->getMTI());
$isoprint .= pack('H*', $jak->getBitmap());
$isoprint .= pack('H*', $data[11]);
$isoprint .= $data[41];
$isoprint .= $data[42];
$isoLength = strlen($isoprint);
echo $isoLength."\n\n\n";
$isoprint = pack('N*', $isoLength).$isoprint;
print_r($isoprint);echo "\n\n\n";
echo $isoServer->sendMessage($isoprint);
echo "\n\n\n";
?>