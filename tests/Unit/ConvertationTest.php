<?php

use Maestroerror\HeicToJpg;

test('Converts and saves to the file', function () {
    $resultImg = "saved-as-jpg.jpg";
    // Save converted image
    HeicToJpg::convert(__dir__ . "/images/image1.heic")->saveAs($resultImg);
    // Check mime type
    $mime = mime_content_type($resultImg);
    // Check image exists
    expect(file_exists($resultImg))->toBeTrue();
    // Check image is JPEG
    expect($mime == "image/jpeg")->toBeTrue();
    // Remove image
    unlink($resultImg);
});


test('Converts and gives content', function () {
    $resultImg = "saved-with-php.jpg";
    // Get content of converted image
    $jpg = HeicToJpg::convert(__dir__ . "/images/image1.heic")->get($resultImg);
    // Save image
    file_put_contents($resultImg, $jpg);
    // Get mime type
    $mime = mime_content_type($resultImg);
    // Check image exists
    expect(file_exists($resultImg))->toBeTrue();
    // Check image is JPEG
    expect($mime == "image/jpeg")->toBeTrue();
    // Remove image
    unlink($resultImg);
});