<?php
$data="9F0206000000000100";
$data_element = Array ('ans',999,1);
echo str_pad(strval(strlen($data)),strlen($data_element[1])+1, "0", STR_PAD_LEFT).$data."\n";
var_dump(str_pad(strval(strlen($data)),strlen($data_element[1])+1, "0", STR_PAD_LEFT).$data);
echo "\n\n";
echo base_convert("FAA57088694EF194",16,2);
echo "\n";
var_dump(base_convert("FAA57088694EF194",16,2));
?>
