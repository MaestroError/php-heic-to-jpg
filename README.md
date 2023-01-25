# php-heic-to-jpg
The easiest way to convert HEIC images to JPEG with PHP and Laravel framework.     

## Installation
```
composer require maestroerror/php-heic-to-jpg
```

## Usage
Using the class "HeicToJpeg" is extremely simple. You need full location of any HEIC image to pass in "convert" function and call "saveAs" (save as file) or "get" (get file contents) methods.
```php
// save
maestroerror\HeicToJpg::convert("image1.heic")->saveAs("image1.jpg");
// 2. get content (binary) of converted JPG
$jpg = maestroerror\HeicToJpg::convert("image1.heic")->get();
```

## Credits
- heif parser by @bradfitz (https://github.com/go4org/go4/tree/master/media/heif)
- libde265 (https://github.com/strukturag/libde265)
- implementation learnt from libheif (https://github.com/strukturag/libheif)
- Edd Turtle (https://gophercoding.com/convert-heic-to-jpeg-go/)