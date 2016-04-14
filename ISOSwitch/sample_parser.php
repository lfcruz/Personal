<?php
include_once 'isoPackager.php';

$iso	= '0800822000000000000004000000000000000516063439749039301';

$jak	= new isoPack();

//add data
$jak->addISO($iso);


//get parsing result
print 'ISO: '. $iso. "\n";
print 'MTI: '. $jak->getMTI(). "\n";
print 'Bitmap: '. $jak->getBitmap(). "\n";
print 'Data Element: '; print_r($jak->getData());
echo "Testing B24enc: ".base64_encode($jak->getISO());
base



?>