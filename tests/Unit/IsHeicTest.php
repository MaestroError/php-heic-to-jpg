<?php 

use Maestroerror\HeicToJpg;

test('Checks heic file correctly', function () {
    // Check image is Heic format
    $fileIsHeic = HeicToJpg::isHeic(__dir__ . "/images/image1.heic");
    expect($fileIsHeic)->toBeTrue();
});

test('Checks jpg file correctly', function () {
    // Check image is not Heic format
    $fileIsHeic = HeicToJpg::isHeic(__dir__ . "/images/apple.jpg");
    expect(!$fileIsHeic)->toBeTrue();
});

test('Checks converted from heic to jpg file correctly', function () {
    // Check image is not Heic format
    $fileIsHeic = HeicToJpg::isHeic(__dir__ . "/images/jpg-from-heic.jpg");
    expect(!$fileIsHeic)->toBeTrue();
});