# php-heic-to-jpg
The easiest way to convert HEIC/HEIF images to JPEG with PHP and Laravel framework. It uses binary file created with Go language and has no dependencies on any other PHP libraries, extensions or third-part software       
- [Installation](#installation)
- [Usage](#usage)
    - [For MacOS users](#for-macos-users)
    - [Force arm64 for linux](#force-arm64-for-linux)
    - [isHeic method](#isheic-method)
    - [convertFromUrl method](#convertfromurl-method)
    - [Mdat issue](##handling-mdat-file-conversion-issues)
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
It should detect the OS itself, but if you want to specify architecture, it is recommended to use `convertOnMac` instead. The second argument is architecture of your system, by default set as "amd64", but you can specify "arm64" (AArch64, M1)
```php
// By default
Maestroerror\HeicToJpg::convertOnMac("image1.heic", "arm64")->saveAs("image1.jpg");
```
         
#### Force arm64 for linux
In case of linux, for some reason, if it doesn't detect your architecture correct or just the `php-heic-to-jpg-linux-arm64` binary is working for you well, you can force it to use in `convert` and `convertFromUrl` by passing true as third argument:
```php
Maestroerror\HeicToJpg::convert("image1.heic", "", true)->saveAs("image.jpg");
```

#### isHeic method      
Before converting, you can use the isHeic method (contributed by [pbs-dg](https://github.com/pbs-dg)) to check if a file is HEIC format.
```php
$fileIsHeic = HeicToJpg::isHeic("image.heic");
if ($fileIsHeic) {
    // Your code
}
```

#### convertFromUrl method      
If your image is available publicly, you can easily convert and save it in your file system using `convertFromUrl` method:
```php
Maestroerror\HeicToJpg::convertFromUrl("https://github.com/MaestroError/php-heic-to-jpg/raw/maestro/tests/Unit/images/image1.heic")->saveAs("image1.jpg");
```

#### Handling 'mdat' File Conversion Issues

If you encounter an issue where the module cannot convert certain images produced by Samsung devices (detailed in this [issue](https://github.com/MaestroError/php-heic-to-jpg/issues/15)), resulting in the error `error reading "meta" box: got box type "mdat" instead`, you can take the following steps:

[heif-converter-image](https://github.com/MaestroError/heif-converter-image) is already required by composer in this (php-heic-to-jpg) package. `heif-converter-image` depends on [libheif](https://github.com/strukturag/libheif) and provides installation scripts for various platforms refer to it's [documentation](https://github.com/MaestroError/heif-converter-image).

- Ensure you have `maestroerror/heif-converter` required in composer by running `composer require maestroerror/heif-converter`.

- Make sure libheif is installed on your system. You can check the [libheif](https://github.com/strukturag/libheif) for installation instructions or use installation script for your platform provided by [heif-converter-image](https://github.com/MaestroError/heif-converter-image).

The `php-heic-to-jpg` package automatically detects the presence of the `heif-converter-image` package and will attempt to use its Command Line Interface (CLI) executable for conversion if default conversion fails.

In case the package cannot find the `heif-converter-image` CLI, you can specify the path as an argument in the `convert` and `convertOnMac` methods like so:

```php
HeicToJpg::convert("image.heic", "path/to/your/bin/heif-converter-{linux/windows/macos}")->saveAs("image.jpg");
HeicToJpg::convertOnMac("image.heic", "arm64", "path/to/your/bin/heif-converter-macos")->saveAs("image.jpg");
HeicToJpg::convertFromUrl("SOME_URL", "path/to/your/bin/heif-converter-{linux/windows/macos}")->saveAs("image.jpg");
```
With these steps, you should be able to handle the conversion of images that were previously causing issues.

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


##### To Do
- Find out if it can be used with docker CLI command from PHP, add in docs if yes
        