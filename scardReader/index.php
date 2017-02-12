<?php

# Get a PC/SC context
$context = scard_establish_context();
echo "context : ";
var_dump($context);

# Get the reader list
$readers = scard_list_readers($context);
echo "readers list : ";
var_dump($readers);

# Use the first reader
$reader = $readers[0];

do{
# Connect to the card
$connection = scard_connect($context, $reader);
}while(!$connection);
echo "connection : ";
var_dump($connection);

//# Select Applet APDU
//$CMD = "00A404000AA00000006203010C0601";
//$res = scard_transmit($connection, $CMD);
//var_dump($res);

# test APDU
$CMD['set'] = "FFCA000000";
$CMD['key'] = "FF82000006000000000000";
$CMD['keyvalid'] = "FF860000050100006000";
$CMD['sector10'] = "FFB0000010";
$CMD['sector11'] = "FFB0000110";
$CMD['sector12'] = "FFB0000210";
$CMD['sector13'] = "FFB0000310";
$res['set'] = scard_transmit($connection, $CMD['set']);
$res['key'] = scard_transmit($connection, $CMD['key']);
$res['keyvalid'] = scard_transmit($connection, $CMD['keyvalid']);
$res['sector10'] = scard_transmit($connection, $CMD['sector10']);
$res['sector11'] = scard_transmit($connection, $CMD['sector11']);
$res['sector12'] = scard_transmit($connection, $CMD['sector12']);
$res['sector13'] = scard_transmit($connection, $CMD['sector13']);
var_dump($res);
//echo pack("H*", $res), "\n";

# Release the PC/SC context
scard_release_context($context);

?>
