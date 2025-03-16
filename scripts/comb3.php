<?php

function mirror ($b)
{
	$res = 0;
	$mask = 0x80;
	for ($i=0; $i<8; $i++)
	{
		if ($b & 1) $res = $res | $mask;
		$b = $b >> 1;
		$mask = $mask >> 1;
	}
	return $res & 0xFF;
}

	$f = fopen('dead_cat.txt', 'r');
	$g = fopen('dead_cat_out.txt', 'w');
	while (!feof($f))
	{
		$s = fgets($f);
		$s = trim($s);
		if (strlen($s) == 0) continue;
		$arr1 = explode("\t", $s);
		fputs($g, "\t.byte\t");

		$arr3 = explode(',', $arr1[1]);
		if (count($arr3) !== 8) { echo "error in $s\n"; exit(1); }

		for ($i=0; $i<8; $i++) {
			$sb = $arr3[$i];
			$b = intval($sb, 8);
			$b = mirror($b);
			$arr3[$i] = str_pad(decoct($b), 3, '0', STR_PAD_LEFT);
			fputs($g, $arr3[$i]);
			if ($i != 7) fputs($g, ', ');
		}
		fputs($g, "\n");
	}
	fclose($f);
	fclose($g);
?>