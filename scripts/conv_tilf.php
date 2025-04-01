<?php
	$f = fopen('s2tilf.txt', 'r');
	$g = fopen('s2tilf_out.txt', 'w');
    $img = imagecreate(1024, 1024);
    $dot0_color = imagecolorallocate($img, 0, 0, 0);
    $dot1_color = imagecolorallocate($img, 255, 255, 255);

    $cur_x = 0;
    $cur_y = 0;

function setDots ($b)
{
    global $cur_x, $cur_y, $img, $dot0_color, $dot1_color;
    for ($i=0; $i<8; $i++)
    {
        if ($b & 0x01) imagesetpixel($img, $cur_x+$i, $cur_y, $dot1_color);
            else imagesetpixel($img, $cur_x+$i, $cur_y, $dot0_color);
        $b = $b >> 1;
    }
    $cur_y++;
    if (($cur_y%8)==0) {
        $cur_y = 0;
        $cur_x += 8;
        if ($cur_x >= 1024) {
            $cur_x = 0;
            $cur_y += 8;
        }
    }
}

    while (!feof($f))
	{
		$s = fgets($f);
		$s = trim($s);
		if (strlen($s) == 0) continue;
		$arr1 = explode(".BYTE", $s);
		$arr3 = explode(',', $arr1[1]);
		if (count($arr3) !== 16) { echo "error in $s\n"; exit(1); }
        // swap mask and data bytes to make words
        fputs($g, "\t.word\t");
		for ($i=0; $i<16; $i+=4) {
			$bm1 = intval(trim($arr3[$i+0]), 8);
			$bm2 = intval(trim($arr3[$i+2]), 8);
            $wm = $bm1 + ($bm2 << 8);
			$b1 = intval(trim($arr3[$i+1]), 8);
			$b2 = intval(trim($arr3[$i+3]), 8);
            setDots($b1);
            setDots($b2);
            $w = $b1 + ($b2 << 8);
			fputs($g, str_pad(decoct($wm), 6, '0', STR_PAD_LEFT) . ",");
			fputs($g, str_pad(decoct($w), 6, '0', STR_PAD_LEFT) . ", ");
		}
        // attr byte -> word
		$s = fgets($f);
		$s = trim($s);
		if (strlen($s) == 0) { echo "error in attr byte: $s\n"; exit(1); }
		$arr1 = explode(".BYTE", $s);
		$arr3 = explode(',', $arr1[1]);
		if (count($arr3) !== 1) { echo "error in $s\n"; exit(1); }
        $ba = intval(trim($arr3[0]), 8);
        fputs($g, str_pad(decoct($ba), 6, '0', STR_PAD_LEFT) . "\n");
	}
	fclose($f);
	fclose($g);

    imagepng($img, "ftiles.png");
?>