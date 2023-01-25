<?php

include "src/HeicToJpg.php";

Maestroerror\HeicToJpg::convert("image1.heic")->saveAs("jpg-from-php.jpg");