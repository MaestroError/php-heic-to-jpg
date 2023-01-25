<?php

include "src/HeicToJpg.php";

maestroerror\HeicToJpg::convert("image1.heic")->saveAs("jpg-from-php.jpg");