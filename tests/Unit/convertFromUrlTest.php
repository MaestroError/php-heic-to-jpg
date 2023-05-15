<?php

use Maestroerror\HeicToJpg;

test('Converts from URL and saves to the file', function () {
    $resultImg = "saved-from-url-as-jpg.jpg";
    // Save converted image
    HeicToJpg::convertFromUrl("https://github.com/MaestroError/php-heic-to-jpg/raw/maestro/tests/Unit/images/image1.heic")->saveAs($resultImg);
    // Check mime type
    $mime = mime_content_type($resultImg);
    // Check image exists
    expect(file_exists($resultImg))->toBeTrue();
    // Check image is JPEG
    expect($mime == "image/jpeg")->toBeTrue();
    // Remove image
    unlink($resultImg);
});