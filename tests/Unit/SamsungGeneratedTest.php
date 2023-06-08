<?php

use Maestroerror\HeicToJpg;

// test("Converts samsung-generated image to JPEG and saves to the file", function () {
//     $resultImg = "saved-as-jpg.jpg";
//     // Save converted image
//     HeicToJpg::convert(__dir__ . "/images/samsung-generated.heic", "/home/maestroerror/Desktop/php-heic-to-jpg/vendor/bin/heif-converter-linux")->saveAs($resultImg);
//     // Check mime type
//     $mime = mime_content_type($resultImg);
//     // Check image exists
//     expect(file_exists($resultImg))->toBeTrue();
//     // Check image is JPEG
//     expect($mime == "image/jpeg")->toBeTrue();
//     // Remove image
//     unlink($resultImg);
// });