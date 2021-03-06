<?php

/* chart.php */ 
$data = array( 
"Jan" => 55, 
"Feb" => 54, 
"Mar" => 53, 
"Apr" => 33, 
"May" => 13, 
"Jun" => 15, 
"Jul" => 23, 
"Aug" => 28, 
"Sep" => 32, 
"Oct" => 45, 
"Nov" => 73, 
"Dec" => 71); 

// create image 
$width = 480; 
$height = 250; 
$image = imagecreate($width, $height); 

// colors 
$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF); 
$navy = imagecolorallocate($image, 0x00, 0x00, 0x80); 
$black = imagecolorallocate($image, 0x00, 0x00, 0x00); 
$gray = imagecolorallocate($image, 0xC0, 0xC0, 0xC0); 

// layout 
$maxval = max($data); 
$nval = sizeof($data); 

$vmargin = 20; // top (bottom) vertical margin for title (x-labels) 
$hmargin = 38; // left horizontal margin for y-labels 

$base = floor(($width - $hmargin) / $nval); // distance between columns 

$ysize = $height - 2 * $vmargin; // y-size of plot 
$xsize = $nval * $base; // x-size of plot 

// title 
$titlefont = 3; 
$title = "Presidential Approval Ratings 2000 (in %)"; 

$txtsz = imagefontwidth($titlefont) * strlen($title); // pixel-width of title 

$xpos = (int)($hmargin + ($xsize - $txtsz)/2); // center the title 
$xpos = max(1, $xpos); // force positive coordinates 
$ypos = 3; // distance from top 

imagestring($image, $titlefont, $xpos, $ypos, $title , $black); 

// y labels and grid lines 
$labelfont = 2; 
$ngrid = 4; // number of grid lines 

$dydat = $maxval / $ngrid; // data units between grid lines 
$dypix = $ysize / ($ngrid + 1); // pixels between grid lines 

for ($i = 0; $i <= ($ngrid + 1); $i++) { 
    // iterate over y ticks 

    // height of grid line in units of data 
    $ydat = (int)($i * $dydat); 

    // height of grid line in pixels
    $ypos = $vmargin + $ysize - (int)($i*$dypix); 

    $txtsz = imagefontwidth($labelfont) * strlen($ydat); // pixel-width of label 
    $txtht = imagefontheight($labelfont); // pixel-height of label 

    $xpos = (int)(($hmargin - $txtsz) / 2); 
    $xpos = max(1, $xpos); 

    imagestring($image, $labelfont, $xpos, 
        $ypos - (int)($txtht/2), $ydat, $black); 

    if (!($i == 0) && !($i > $ngrid)) {
        imageline($image, $hmargin - 3, 
            $ypos, $hmargin + $xsize, $ypos, $gray); 
        // don't draw at Y=0 and top 
    }
} 

// columns and x labels 
$padding = 3; // half of spacing between columns 
$yscale = $ysize / (($ngrid+1) * $dydat); // pixels per data unit 

for ($i = 0; list($xval, $yval) = each($data); $i++) { 

    // vertical columns 
    $ymax = $vmargin + $ysize; 
    $ymin = $ymax - (int)($yval*$yscale); 
    $xmax = $hmargin + ($i+1)*$base - $padding; 
    $xmin = $hmargin + $i*$base + $padding; 

    imagefilledrectangle($image, $xmin, $ymin, $xmax, $ymax, $navy); 

    // x labels 
    $txtsz = imagefontwidth($labelfont) * strlen($xval); 

    $xpos = $xmin + (int)(($base - $txtsz) / 2); 
    $xpos = max($xmin, $xpos); 
    $ypos = $ymax + 3; // distance from x axis 

    imagestring($image, $labelfont, $xpos, $ypos, $xval, $black); 
} 

// plot frame 
imagerectangle($image, $hmargin, $vmargin, 
    $hmargin + $xsize, $vmargin + $ysize, $black); 

// flush image 
header("Content-type: image/png"); // or "Content-type: image/png" 
imagepng($image); // or imagepng($image) 
imagedestroy($image); 

?>