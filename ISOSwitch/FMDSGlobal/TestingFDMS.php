<?php
include_once 'isoPackagerFDMS.php';
include_once 'socketServer.php';

$jak	= new isoPack();
$isoprint = "";
$isoServer = new socketProcessor("localhost", 28583, "C");

$jak->addMTI("0100");
//$jak->addData(2, "4012000033330026");
//$jak->addData(2, "4921062576101008");
$jak->addData(2, "5424180279791732");
$jak->addData(3, "000000");
$jak->addData(4, "15.14");
$jak->addData(7, gmdate('mdHis'));
$jak->addData(11, "000025");
$jak->addData(12, date('His'));
$jak->addData(13, date('md'));
$jak->addData(14, "2010");
//$jak->addData(14, "2105");
//$jak->addData(14, "2010");
$jak->addData(18, "4722");
$jak->addData(22, "018");
$jak->addData(24, "001");
$jak->addData(25, "08");
$jak->addData(31, "01");
//$jak->addData(37, "1");
$jak->addData(41, "101017389");
$jak->addData(42, "39039800016");
$jak->addData(49, "214");
//$jak->addData(59, " ");
switch (substr($jak->getBit(2),2,1)) {
    case "4":
        $jak->addData(63, "0048313459202020202020202020202020202020202020202020303030303030303030303030303030303030303030303030002336393031310B2020202020000000000000545041373737001253445449303030303030303000025649");
        break;
    case "5":
        $jak->addData(63, "00483134592020202020202020202020202020202020202020203030303030303030303030303030303030303030303030300060333631383039343338303737313030303030303030303032383536373030303030303030303030303030303030303030303030303030303030303030000734393020202020002336393031310B202020202000000000000054504137373700144D43303156303236303330303430303535303630303753303853303935313030313131313230001253445449303030303030303000025649");
        break;
    default:
        $jak->addData(63, "00483134592020202020202020202020202020202020202020203030303030303030303030303030303030303030303030300060333631383039343338303737313030303030303030303032383536373030303030303030303030303030303030303030303030303030303030303030007343930202020200002336393031310B2020202020000000000000545041373737001253445449303030303030303000025649");
        break;
}

echo 'ISO: '. $iso. "\n";
echo 'MTI: '. $jak->getMTI(). "\n";
echo 'Bitmap: '. $jak->getBitmap(). "\n";
echo 'Data Element: '; print_r($jak->getData());echo "\n\n\n";

$data = $jak->getData();
$isoprint .= pack('H*', $jak->getMTI());
$isoprint .= pack('H*', $jak->getBitmap());
$isoprint .= pack('H*', $data[2]);
$isoprint .= pack('H*', $data[3]);
$isoprint .= pack('H*', $data[4]);
$isoprint .= pack('H*', $data[7]);
$isoprint .= pack('H*', $data[11]);
$isoprint .= pack('H*', $data[12]);
$isoprint .= pack('H*', $data[13]);
$isoprint .= pack('H*', $data[14]);
$isoprint .= pack('H*', $data[18]);
$isoprint .= pack('n*', $data[22]);
$isoprint .= pack('n*', $data[24]);
$isoprint .= pack('H*', $data[25]);
$isoprint .= pack('H*', $data[31]);
//$isoprint .= $data[37];
$isoprint .= $data[41];
$isoprint .= $data[42];
$isoprint .= pack('H*', $data[49]);
//$isoprint .= $data[59];
$isoprint .= pack('n*', substr($data[63], 0, 3));
$isoprint .= pack('H*', substr($data[63], 3, strlen($data[63])-3));
$isoLength = strlen($isoprint);
echo $isoLength."\n\n\n";
$isoprint = pack('n*', $isoLength).$isoprint;
$isoprint = pack('H*',"02464402").$isoprint;
$isoprint .= pack('H*',"03464403");
print_r($isoprint);echo "\n\n\n";
echo $isoServer->sendMessage($isoprint);
echo "\n\n\n";
?>