<?php

include "src/HeicToJpg.php";

$jpg = Maestroerror\HeicToJpg::convert("image1.heic")->get();
file_put_contents("saved-with-php.jpg", $jpg);