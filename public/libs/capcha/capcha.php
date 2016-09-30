<?php
session_start();   
$captchanumber = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; 
$captchanumber = substr(str_shuffle($captchanumber), 0, 8); 
$_SESSION["code_capcha"] = $captchanumber;      
$font = 'OpenSans-Regular';  
$im = imagecreatetruecolor(200, 40);
$text_color = imagecolorallocate($im, 255, 255, 255);
imagettftext($im, 20, 0, 35, 27, $text_color, $font, $captchanumber);   
header('Content-type: image/png');    
imagejpeg($im);
imagedestroy($im);