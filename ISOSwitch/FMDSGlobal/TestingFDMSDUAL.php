<?php
include_once 'isoPackagerFDMSDUAL.php';
include_once '../lib/socketServer.php';

$jak = new isoPack();
$rak = new isoPack();
$isoprint = "";
$isoServer = new socketProcessor("localhost", 28583, "C");

$jak->addMTI("0200");
$jak->addData(2, "5962448094380771424");
$jak->addData(3, "004000");
$jak->addData(4, "1.00");
$jak->addData(11, "000090");
$jak->addData(12, @date('His'));
$jak->addData(13, @date('md'));
$jak->addData(14, "1806");
$jak->addData(22, "0011");
$jak->addData(24, "0051");
$jak->addData(25, "00");
//$jak->addData(31, "1");
$jak->addData(35, "5894288094380771424D53664553788498743");
$jak->addData(41, "1480270");
$jak->addData(42, "1137225");
$jak->addData(53, "1706020947320000");
$jak->addData(62, "000003");
$jak->addData(63, "0004363630380030393330313120202020202020202020202020202020202020463230383031000853444D4930303131");

echo 'MTI: '. $jak->getMTI(). "\n";
echo 'Bitmap: '. $jak->getBitmap(). "\n";
echo 'Data Element: '; print_r($jak->getData());echo "\n\n\n";

$data = $jak->getData();
$isoprint .= pack('H*', $jak->getMTI());
$isoprint .= pack('H*', $jak->getBitmap());
$isoprint .= pack('H*', $data[2]);
$isoprint .= pack('H*', $data[3]);
$isoprint .= pack('H*', $data[4]);
$isoprint .= pack('H*', $data[11]);
$isoprint .= pack('H*', $data[12]);
$isoprint .= pack('H*', $data[13]);
$isoprint .= pack('H*', $data[14]);
$isoprint .= pack('H*', $data[22]);
$isoprint .= pack('H*', $data[24]);
$isoprint .= pack('H*', $data[25]);
//$isoprint .= pack('c*', $data[31]);
$isoprint .= pack('H*', $data[35]);
$isoprint .= $data[41];
$isoprint .= $data[42];
$isoprint .= pack('H*', $data[53]);
$isoprint .= pack('n*', substr($data[62], 0, 3));
$isoprint .= substr($data[62], 3, strlen($data[62])-3);
$isoprint .= pack('n*', substr($data[63], 0, 3));
$isoprint .= pack('H*', substr($data[63], 3, strlen($data[63])-3));
$isoprint = pack('H*',"6000530000").$isoprint;
$isoLength = strlen($isoprint);
$isoprint = pack('n*', $isoLength).$isoprint;
echo "############################### RESPONSE #############################################\n";
$response = $isoServer->sendMessage($isoprint);
$isoresponse = unpack('H*', $response);
$rak->addISO(substr($isoresponse[1], 14, strlen($isoprint)-14));

echo 'MTI: '. $rak->getMTI(). "\n";
echo 'Bitmap: '. $rak->getBitmap(). "\n";
echo 'Data Element: '; print_r($rak->getData());echo "\n\n\n";
?>