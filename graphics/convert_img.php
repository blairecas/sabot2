<?php
    $fname = dirname(__FILE__)."/".$argv[1];
    $fname_part = pathinfo($fname, PATHINFO_FILENAME);

    $img = imagecreatefrompng($fname);
    $width = imagesx($img);
    $height = imagesy($img);
    echo "Image $fname: $width x $height\n";
    
    $data_img = Array();
    for ($y=0; $y<$height; $y++)
    {
        for ($bytesx=0; $bytesx<($width/8); $bytesx++)
        {
            $res = 0; 
            for ($x=0; $x<8; $x++)
            {
                $py = $y;
                $px = $bytesx*8 + $x;
                $res = ($res >> 1) & 0x00FFFFFF; 		
                $rgb_index = imagecolorat($img, $px, $py);
                $rgba = imagecolorsforindex($img, $rgb_index);
                $r = $rgba['red'];
                $g = $rgba['green'];
                $b = $rgba['blue'];
                if ($r > 127) $res = $res | 0x00800000;
                if ($g > 127) $res = $res | 0x00008000;
                if ($b > 127) $res = $res | 0x00000080;
            }
            array_push($data_img, $res);
        }
    }
    
    $fname_tm = dirname(__FILE__)."/".$fname_part.".tm";
    $fname_zx = dirname(__FILE__)."/".$fname_part.".zx";
    
    // red
    $f = fopen($fname_tm."r", "w");
    for ($i=0; $i<count($data_img); $i++) fputs($f, chr(($data_img[$i]&0x00FF0000)>>16));
    fclose($f);
    exec(dirname(__FILE__)."/zx0 -f -q ".$fname_tm."r ".$fname_zx."r");
    unlink($fname_tm."r");

    // green
    $f = fopen($fname_tm."g", "w");
    for ($i=0; $i<count($data_img); $i++) fputs($f, chr(($data_img[$i]&0x0000FF00)>>8));
    fclose($f);
    exec(dirname(__FILE__)."/zx0 -f -q ".$fname_tm."g ".$fname_zx."g");
    unlink($fname_tm."g");

    // blue
    $f = fopen($fname_tm."b", "w");
    for ($i=0; $i<count($data_img); $i++) fputs($f, chr($data_img[$i]&0x000000FF));
    fclose($f);
    exec(dirname(__FILE__)."/zx0 -f -q ".$fname_tm."b ".$fname_zx."b");
    unlink($fname_tm."b");
?>