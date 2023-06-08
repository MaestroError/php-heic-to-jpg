<?php

include "src/HeicToJpg.php";

Maestroerror\HeicToJpg::convert("samsung.heic-mdat", "/home/maestroerror/Desktop/php-heic-to-jpg/vendor/bin/heif-converter-linux")->saveAs("jpg-from-php.jpg");