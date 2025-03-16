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

	$f = fopen('backs.txt', 'r');
	$g = fopen('backs_out.txt', 'w');
	while (!feof($f))
	{
		$s = fgets($f);
		$s = trim($s);
		if (strlen($s) == 0) continue;
		if ($s[0] == ';') continue;
		$arr1 = explode(':', $s);
		fputs($g, $arr1[0].":\t.byte\t");
		$arr1[1] = trim($arr1[1]);
		$arr2 = explode("\t", $arr1[1]);
		$arr2[1] = trim($arr2[1]);
		$arr3 = explode(',', $arr2[1]);
		if (count($arr3) !== 9) { echo "error in $s\n"; exit(1); }
// COM
//		for ($i=0; $i<8; $i++) {
//			$sb = $arr3[$i];
//			$b = intval($sb, 8);
//			$b = $b ^ 0xFF;
//			$arr3[$i] = str_pad(decoct($b), 3, '0', STR_PAD_LEFT);
//			fputs($g, $arr3[$i].', ');
//		}
// mirror 
		for ($i=0; $i<8; $i++) {
			$sb = $arr3[$i];
			$b = intval($sb, 8);
			if ($do_mirror) $b = mirror($b);
			$arr3[$i] = str_pad(decoct($b), 3, '0', STR_PAD_LEFT);
			fputs($g, $arr3[$i].', ');
		}
		// attr byte
		fputs($g, $arr3[8]."\n");
	}
	fclose($f);
	fclose($g);
?>