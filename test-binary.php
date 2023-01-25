<?php

include "src/HeicToJpg.php";

$jpg = maestroerror\HeicToJpg::convert("IMG_5016.HEIC")->get();
file_put_contents("saved-with-php.jpg", $jpg);