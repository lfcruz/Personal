<?php
include_once 'isoPackager.php';
include_once 'socketServer.php';

$iso	= '0800002000000080000000000100000345';
$jak	= new isoPack();
$isoprint = "";
$isoServer = new socketProcessor("localhost", 18583, "C");

//add data
//$jak->addISO($iso);
$jak->addMTI("0200");
$jak->addData(3, "960000");
$jak->addData(4, "100.15");
$jak->addData(11, "15");
$jak->addData(12, date('His'));
$jak->addData(13, date('md'));
$jak->addData(22, "51");
$jak->addData(23, "000");
$jak->addData(24, "000");
$jak->addData(25, "00");
$jak->addData(35, "4594140000091494D19122010000015400000");
$jak->addData(41, "173664");
$jak->addData(42, "39457728837");
$jak->addData(52, "FAA57088694EF194");
//$jak->addData(55, "5F2A0201245F34010182021C008407A0000000031010950580000000009A031102249B0268009C01009F02060000000000009F03060000000000009F0607A00000000310109F0802008C9F0902008C9F100706010A039000009F1A0201249F2608423158936ED6C38F9F2701809F3303E0B0C89F34034103029F3501229F360200019F3704ACAC66E89F5800DF0100DF0200DF0400");
$jak->addData(55, "9F02060000000001009F03060000000000009F1A020214950580800400005F2A0202149A031606099C01019F3704EEDA181E82021C009F360200179F34030204009F26083B798129256AF6F79F2701809F100706010A03A0A0009F3303E040009F3501119F090200008C159F02069F03069F1A0295055F2A029A039C019F37048D178A029F02069F03069F1A0295055F2A029A039C019F37048E140000000000000000020102041E051E031F020000");
//$jak->addData(55, "9F0206000000000100");
echo 'ISO: '. $iso. "\n";
echo 'MTI: '. $jak->getMTI(). "\n";
echo 'Bitmap: '. $jak->getBitmap(). "\n";
echo 'Data Element: '; print_r($jak->getData());echo "\n\n\n";

$data = $jak->getData();
$isoprint .= pack('H*', $jak->getMTI());
$isoprint .= pack('H*', $jak->getBitmap());
$isoprint .= pack('H*', $data[3]);
$isoprint .= pack('H*', $data[4]);
$isoprint .= pack('H*', $data[11]);
$isoprint .= pack('H*', $data[12]);
$isoprint .= pack('H*', $data[13]);
$isoprint .= pack('H*', $data[22]);
$isoprint .= pack('H*', $data[23]);
$isoprint .= pack('H*', $data[24]);
$isoprint .= pack('H*', $data[25]);
$isoprint .= pack('H*', $data[35]);
$isoprint .= $data[41];
$isoprint .= $data[42];
$isoprint .= pack('H*', $data[52]);
$isoprint .= pack('H*', $data[55]);
$isoLength = strlen($isoprint);
echo $isoLength."\n\n\n";
$isoprint = pack('N*', $isoLength).$isoprint;
print_r($isoprint);echo "\n\n\n";
echo $isoServer->sendMessage($isoprint);
echo "\n\n\n";
?>