<?php
    $s = file_get_contents("k07126.txt");
    $arr = explode(",", $s);
    $i = 0;
    foreach ($arr as $k => $v)
    {	
	$b = intval($v, 8);
	$b = $b ^ 0xFF;
	if ($i % 16 == 0) echo "\n\t.byte\t";
	echo str_pad(decoct($b), 3, "0", STR_PAD_LEFT);
	if ($i % 16 != 15) echo ", ";
	$i++;
    }
?>
    