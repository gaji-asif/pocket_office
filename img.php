<?php
 

$white = imagecolorallocate($img, 255, 255, 255);

$font = "arial.ttf"; 

//Text To Add
    $text = $_GET['deep'];
    
    //Background Image - The Image To Write Text On
    $image = imagecreatefrompng('images/hailstrom.png');
    
    //Color of Text
    $textColor = imagecolorallocate($image, 255,255,255);
    imagealphablending($image, false);
imagesavealpha($image, true);
    //Full Font-File Path
    $fontPath = "arial.ttf";
    
    //Function That Write Text On Image
    imagettftext($image, 8, 0, 5, 20, $textColor, $fontPath, $text);
    
    //Set Browser Content Type
    header('Content-type: image/png');
    
    //Send Image To Browser
    imagepng($image);
    
    //Clear Image From Memory
    imagedestroy($image);

?>