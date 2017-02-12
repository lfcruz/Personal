<?php

$context = scard_establish_context();
var_dump($context);

$readers = scard_list_readers($context);
var_dump($readers);

$reader = $readers[0];
echo "Using reader: ", $reader, "\n";

echo unpack("H*","3B8F8001804F0CA000000306030001000000006A");
scard_release_context($context);

?>
