# php-heic-to-jpg
The easiest way to convert HEIC/HEIF images to JPEG with PHP and Laravel framework. It uses binary file created with Go language and has no dependencies on any other PHP libraries, extensions or third-part software       
- [Installation](#installation)
- [Usage](#usage)
- [Credits](#credits)
          
## Installation       
*Run command in your project's root directory*
```
composer require maestroerror/php-heic-to-jpg
```

## Usage
Using the class "HeicToJpeg" is extremely simple. You need full location of any HEIC image to pass in "convert" function and call "saveAs" (save as file) or "get" (get file contents) methods.
```php
// 1. save as file
Maestroerror\HeicToJpg::convert("image1.heic")->saveAs("image1.jpg");
// 2. get content (binary) of converted JPG
$jpg = Maestroerror\HeicToJpg::convert("image1.heic")->get();
```
         
#### For MacOS users
It should detect the OS itself, but if you want to specify architecture, it is recommended to use `convertOnMac` instead. The second argument is architecture of your system, by default set as "amd64", but you can specify "arm64" (aarm64, M1)
```php
// By default
Maestroerror\HeicToJpg::convertOnMac("image1.heic", "arm64")->saveAs("image1.jpg");
```

#### isHeic method      
Before converting, you can use the isHeic method (contributed by [pbs-dg](https://github.com/pbs-dg)) to check if a file is HEIC format.
```php
$fileIsHeic = HeicToJpg::isHeic("image.heic");
if ($fileIsHeic) {
    // Your code
}
```

## Credits
I would like to say thanks to these people. Their work helped me to build heicToJpg file with Go:
- heif parser by @bradfitz (https://github.com/go4org/go4/tree/master/media/heif)
- libde265 (https://github.com/strukturag/libde265)
- implementation learnt from libheif (https://github.com/strukturag/libheif)
- Edd Turtle (https://gophercoding.com/convert-heic-to-jpeg-go/)
- crazy-max/xgo (https://github.com/crazy-max/xgo)


#### Log
**27/02/2023**       
Built executables for MacOs (OS X / Darwin) with command `sudo /home/maestroerror/go/bin/xgo --targets=darwin/*  github.com/MaestroError/php-heic-to-jpg`        
**04/03/2023**         
Added pest test and workflows for linux, windows and macos. Run tests locally with `./vendor/bin/pest`