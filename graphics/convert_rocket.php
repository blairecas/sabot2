<?php

    $img = imagecreatefrompng('./rocket.png');
    $width = imagesx($img);
    $height = imagesy($img);
    echo "Image: $width x $height\n";
    $tiles_dx = intval($width / 8);
    $tiles_dy = intval($height / 8);
    echo "Tiles: $tiles_dx x $tiles_dy\n";
    
    // tiles array
    $tilesArray = Array();
    // tiles map
    $tilesMap = Array();
    
    // scan image and create map and array
    for ($tiley=0; $tiley<$tiles_dy; $tiley++)
    {
        for ($tilex=0; $tilex<$tiles_dx; $tilex++)
        {
            // create a tile
	    $tile = Array();
	    for ($y=0; $y<8; $y++)	// tiles will be 64 elements long, 4 sections by 16 elements
            {
                $res = 0; 
		for ($x=0; $x<8; $x++)	// cycle by 4 pix and double them (tile is 32x16 pix)
                {
                    $py = $tiley*8 + $y;
		    $px = $tilex*8 + $x;
		    $res = ($res >> 1) & 0xFFFFFF;
                    $rgb_index = imagecolorat($img, $px, $py);
                    $rgba = imagecolorsforindex($img, $rgb_index);
                    $r = $rgba['red'];
                    $g = $rgba['green'];
                    $b = $rgba['blue'];
		    if ($g > 127) $res = $res | 0b000000001000000000000000;
                    if ($r > 127) $res = $res | 0b000000000000000010000000;
                    if ($b > 127) $res = $res | 0b100000000000000000000000;
                }
                array_push($tile, $res);
            }
	    // now check do we already have this tile
            $found = -1;
	    for ($i=0; $i<count($tilesArray); $i++)
            {
		$diff = array_diff_assoc($tile, $tilesArray[$i]);
                if (count($diff) == 0) {
                    $found = $i;
		    break;
                }
	    }
	    // if not found - add to tilesArray
            if ($found < 0) {
		$found = array_push($tilesArray, $tile) - 1;
	    }
	    // add to tilesMap
	    array_push($tilesMap, $found);
        }
    }
    
    echo "Total tiles in map: ".count($tilesMap)."\n";
    echo "Different tiles count: ".count($tilesArray)."\n";
    
    ////////////////////////////////////////////////////////////////////////////
    
    echo "Writing tiles map ...\n";
    $f = fopen ("rocket.mac", "w");
    fputs($f, "RocketTiles:\n\t.byte\t");
    $total = count($tilesMap);
    for ($i=0; $i<$total; $i++)
    {
	fputs($f, decoct($tilesMap[$i]));
	if ($i%11 != 10) fputs($f, ", "); 
	else if ($i!=($total-1)) fputs($f, "\n\t.byte\t");
    }
    fputs($f, "\n\t.even\n\n");

    echo "Writing CPU tiles data ...\n";
    fputs($f, "RocketTile000:\n");
    for ($t=0; $t<count($tilesArray); $t++)
    {
	$tile = $tilesArray[$t];
	fputs($f, "\t.word\t");
	for ($i=0; $i<8; $i++)
	{
	    fputs($f, decoct($tile[$i] & 0xFFFF));
	    if ($i<7) fputs($f, ", "); else fputs($f, "\n");
	}
    }
    fputs($f, "\n\n");

    echo "Writing PPU tiles data ...\n";
    fputs($f, "RocketTile000:\n");
    for ($t=0; $t<count($tilesArray); $t++)
    {
	$tile = $tilesArray[$t];
	fputs($f, "\t.byte\t");
	for ($i=0; $i<8; $i++)
	{
	    fputs($f, decoct(($tile[$i] & 0xFF0000)>>16));
	    if ($i<7) fputs($f, ", "); else fputs($f, "\n");
	}
    }
    fputs($f, "\n\n");

    fclose($f);
    
?>