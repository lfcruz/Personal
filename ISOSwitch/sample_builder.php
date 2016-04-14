<?php
include_once('isoPackager.php');
include_once('socketServer.php');

$isoMsg = new isoPack();
$isoRsp = new isoPack();
$isoProc = Array();
$isoSRV = new socketServer('0.0.0.0', 18583);

function getResponse($vMTI, $vIsoData) {
    switch ($vMTI) {
        case "0100":
            break;
        case "0200":
            break;
        case "0400":
            break;
        default:
            break;
    }
}

//Main -------------------------------------------------------------------------
do {
    $isoSRV->openStream();
    $isoMsg->addISO($isoSRV->inputStream());
    if ($isoMsg->validateISO()) {
        echo "<<<<<<<<<< Incoming.................................................\n";
        echo $isoMsg->getISO()."\n";
        echo "+++++++++++++++++++++++++++ Parsed ISO +++++++++++++++++++++++++++++++\n";
        var_dump($isoMsg->getData());
        echo "\n+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n\n";
        echo "Start processing message: ".date("ymdHis")."\n";
        $isoProc = getResponse($isoMsg->getMTI(), $isoMsg->getData());
        $isoRsp->addMTI($isoProc["mti"]);
        $isoRsp->addISO($isoProc["iso"]);
        $isoSRV->outputStream($isoRsp->getISO());
        echo "Finish processing message: ".date("ymdHis")."\n";
        echo ">>>>>>>>>>> Outgoing................................................\n";
        echo $isoRsp->getISO()."\n";
        echo "++++++++++++++++++++++++++++ Parsed ISO ++++++++++++++++++++++++++++++\n";
        var_dump($isoRsp->getData());
        echo "\n+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n\n";
        echo "\n\n\n";
    } else {
        echo "ERROR!!!! - Invalid ISO Message.\n\n\n\n";
    }
            
} while (true);



$isoMsg->addMTI('0200');
$isoMsg->addData(2,'4594160000033825');    //459414000091494
$isoMsg->addData(3,'010014');
$isoMsg->addData(4,'50000');
$isoMsg->addData(7,date("mdHis"));
$isoMsg->addData(11,rand(100000,999999));
$isoMsg->addData(12,date("His"));
$isoMsg->addData(13,date("md"));
$isoMsg->addData(14,'1219');
$isoMsg->addData(18,'6011');
$isoMsg->addData(19,'214');
$isoMsg->addData(22,'051');
$isoMsg->addData(23,'002');
$isoMsg->addData(25,'00');
$isoMsg->addData(32,'46910');
$isoMsg->addData(37,rand(100000,999999));
$isoMsg->addData(41,'439');	// DKT Terminal ID
$isoMsg->addData(42,'440');	// DKT Agency ID
$isoMsg->addData(43,'Resp. Caleta #33, Matahambre, Sto. Dgo.');	// DKT Agency Address Data
$isoMsg->addData(48,'5n00000');
$isoMsg->addData(49,'214');
$isoMsg->addData(52,'1234');
$isoMsg->addData(55,'?????');  // ARQd ICC info.
$isoMsg->addData(128,'LlaveEncryptPIN');	// ENC Key

echo $isoMsg->getISO()."\n\n";
var_dump($isoMsg->getData());
?>