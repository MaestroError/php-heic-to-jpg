<?php

include "src/HeicToJpg.php";

maestroerror\HeicToJpg::convert("IMG_5016.HEIC")->saveAs("jpg-from-php.jpg");