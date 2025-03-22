<?php
	$b1 = 0114;
	$b2 = 0144;
	$w1 = $b1 | ($b2 << 8);
	echo decoct($w1)." $w1\n";
	echo decoct($w1+043310)."\n";
	echo "\n";
	$w1 += 7*32;
	echo decoct($w1&0xFF)." ".decoct($w1>>8)."\n";
?>