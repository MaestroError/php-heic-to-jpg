<?php

include "src/HeicToJpg.php";

$path	= "test-binary-bad.heic";
$dest	= "test-binary-bad.jpg";

if(Maestroerror\HeicToJpg::isHeic($path)) {
    // 1. save as file
    try{
        $result = \Maestroerror\HeicToJpg::convert($path)->saveAs($dest);
    }catch (\Exception $e) {
        return explode(':', $e->getMessage())[0];
    }
    
    // Works
    $jpg = file_get_contents($dest);
    $base64=base64_encode($jpg);
    echo "<img src='data:image/jpeg;base64, $base64'/>";
    
    // Works
    $jpg = \Maestroerror\HeicToJpg::convert($path)->get();
    $base64=base64_encode($jpg);
    echo "<img src='data:image/jpeg;base64, $base64'/>";
}