<?php

namespace Maestroerror;

class HeicToJpg {

    /**
     * Stores binary content of JPG file
     */
    private string $binary;

    /**
     * Stores converted JPG file location
     */
    private string $jpg;

    /**
     * Executable file name from bin folder
     */
    protected string $exeName = "heicToJpg";

    /**
     * OS of server
     */
    protected string $os = "linux";

    /**
     * Architecture of server
     */
    protected string $arch = "amd64";

    /**
     * Force arm64
     */
    protected bool $forceArm = false;

    /**
     * Location of the "heif-converter-image" package's executable
     */
    protected string $libheifConverterLocation = "";

    protected string $libheifOutput = "";

    /**
     * Takes full location of file as a string
     */
    public function convertImage(string $source): self {
        $this->checkLinuxOS();
        $this->processImage($source);
        $this->extractBinary();
        return $this;
    }

    /**
     * The same as convertImage but for MacOS users
     */
    public function convertImageMac(string $source, string $arch = "amd64"): self {
        $this->setDarwinExe($arch);
        $this->processImage($source);
        $this->extractBinary();
        return $this;
    }

    /**
     * Saves JPG file as $path (Full location is preferable)
     */
    public function saveAs(string $path): bool {
        file_put_contents($path, $this->binary);
        return $this->exit();
    }

    /**
     * Removes temporary JPG file and returns its content (binary)
     */
    public function get(): string {
        $this->exit();
        return $this->binary;
    }
    
    public function setConverterLocation(string $path): self {
        $this->libheifConverterLocation = $path;
        return $this;
    }

    /**
     * Checks is used on macOS or not
     */
    public function checkMacOS(): self {
        $os = strtolower(php_uname('s'));
        $arch = strtolower(php_uname('m'));

        if (self::stringContains($os, 'macos')
            || self::stringContains($os, 'os x')
            || self::stringContains($os, 'darwin')
            || self::stringContains($os, 'macintosh')
        ) {
            $this->os = "darwin";
        }

        if (self::stringContains($arch, "x86_64") || self::stringContains($arch, "amd64")) {
            $this->arch = "amd64";
        }

        if (self::stringContains($arch, "arm")) {
            $this->arch = "arm64";
        }

        $this->checkDarwinExe();

        return $this;
    }

    public function checkLinuxOS(): self {
        $os = strtolower(php_uname('s'));
        $arch = strtolower(php_uname('m'));

        if (self::stringContains($os, 'linux')) {
            $this->os = "linux";
        }

        if (self::stringContains($arch, "aarch64") || self::stringContains($arch, "arm64")){
            $this->arch = "arm64";
        }

        // Fix for the Debian (10/11 Versions), visit the issue for more info (https://github.com/MaestroError/php-heic-to-jpg/issues/22)
        if ($this->forceArm) {
            $this->arch = "arm64";
        }

        $this->checkLinuxExe();

        return $this;
    }

    public function checkWindowsOS(): self {
        $os = strtolower(php_uname('s'));

        if (self::stringContains($os, 'windows') || self::stringContains($os, 'win')) {
            $this->os = "windows";
        }

        $this->checkWindowsExe();
        
        return $this;
    }

    public function checkOS($forceArm = false): self {
        $this->forceArm = $forceArm;
        return $this->checkWindowsOS()->checkLinuxOS()->checkMacOS();
    }

    /**
     * Runs heicToJpg CLI tool to convert file
     */
    protected function processImage(string $source): void {
        $source = htmlspecialchars($source);
        $newFileName = $source . "-" . uniqid(rand(), true);
        $exeName = $this->exeName;
        $command = __DIR__.'/../bin/'.$exeName.' "'.$source.'" "'.$newFileName.'" 2>&1';
        exec($command, $output);
        foreach ($output as $line) {
            $parsed = $this->getStringBetween($line, '--', '--');
            if (!empty($parsed)) {
                $this->jpg = $parsed;
                break;
            }
        }
        if (empty($this->jpg)) {
            $error = \is_array($output) ? implode("\\n", $output) : $output;
            // Try to convert with libheif
            if (!$this->tryToConvertWithLibheif($source, $newFileName)) {
                if(file_exists($newFileName)){
                    unlink($newFileName);
                }
                throw new \RuntimeException("Couldn't convert HEIC to JPG: '" . $error . "' | Bin used: '" . $this->exeName . "' HEIC: '" . $source . "' Full Command: '" . $command . "'" . " Output from heif-converter-image exe: " . $this->libheifOutput);
            }
        }
    }

    protected function tryToConvertWithLibheif(string $source, string $newFile): bool {
        // ./vendor/bin/heif-converter-linux heic input.heic output.png
        if (empty($this->libheifConverterLocation)) {
            $this->libheifConverterLocation = __DIR__.'/../bin/' . "heif-converter-" . $this->os;
        }
        // If libheif converter is available, try to convert
        if (file_exists($this->libheifConverterLocation)) {
            $newFile = $newFile . ".jpg";
            $command = $this->libheifConverterLocation . ' heic "' . $source . '" "' . $newFile . '"';
            exec($command, $output);
            if (file_exists($newFile)) {
                $this->jpg = $newFile;
                return true;
            } else {
                foreach ($output as $line) {
                    $this->libheifOutput .= $line;
                }
                return false;
            }
        }
        return false;
    }

    /**
     * Read the content of file
     */
    protected function extractBinary(): void {
        $this->binary = file_get_contents($this->jpg) ?: '';
    }

    /**
     * Returns string between $start and $end
     */
    private function getStringBetween(string $string, string $start, string $end): string {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * Removes converted JPG file
     *
     * @throws \Exception if JPG file does not exist
     */
    private function exit(): bool {
        if(file_exists($this->jpg)) {
            unlink($this->jpg);
            return true;
        } else {
            throw new \Exception("JPG doesn't exist");
        }
    }

    private function checkLinuxExe(): void
    {
        if ($this->os == "linux" && $this->arch == "arm64") {
            $this->exeName = "php-heic-to-jpg-linux-arm64";
        }
    }

    private function checkWindowsExe(): void
    {
        if ($this->os == "windows") {
            $this->exeName = "heicToJpg.exe";
        }
    }

    /**
     * Check os and arch properties to set executable name correctly
     */
    private function checkDarwinExe(): void {
        if ($this->os == "darwin" && $this->arch == "amd64") {
            $this->exeName = "php-heic-to-jpg-darwin-amd64";
        }
        if ($this->os == "darwin" && $this->arch == "arm64") {
            $this->exeName = "php-heic-to-jpg-darwin-arm64";
        }
    }

    /**
     * Sets macOS executable by architecture
     */
    private function setDarwinExe(string $arch): void {
        if ($arch == "arm64") {
            $this->exeName = "php-heic-to-jpg-darwin-arm64";
        } else {
            $this->exeName = "php-heic-to-jpg-darwin-amd64";
        }
    }

    public static function convert(string $source, string $converterPath = "", $forceArm = false): self
    {
        return (new self)
            ->checkOS($forceArm)
            ->setConverterLocation($converterPath)
            ->convertImage($source);
    }

    public static function convertOnMac(string $source, string $arch = "amd64", string $converterPath = ""): self
    {
        return (new self)->setConverterLocation($converterPath)->convertImageMac($source, $arch);
    }

    public static function convertFromUrl(string $url, string $converterPath = "", $forceArm = false): self {
        // Download image
        $newFileName = "HTTP" . "-" . uniqid(rand(), true);
        file_put_contents($newFileName, file_get_contents($url));
        // Convert image
        $object = (new self)
            ->checkOS($forceArm)
            ->setConverterLocation($converterPath)
            ->convertImage($newFileName);

        // Remove downloaded image
        unlink($newFileName);

        // Return converted object
        return $object;
    }

    /**
     * Check if file is in HEIC format.
     */
    public static function isHeic(string $path): bool
    {
        $h = fopen($path, 'rb');
        $f = fread($h, 12);
        fclose($h);
        $magicNumber = strtolower(trim(substr($f, 8)));

        $heicMagicNumbers = [
            'heic', // official
            'mif1', // unofficial but can be found in the wild
            'ftyp', // 10bit images, or anything that uses h265 with range extension
            'hevc', // brands for image sequences
            'hevx', // brands for image sequences
            'heim', // multiview
            'heis', // scalable
            'hevm', // multiview sequence
            'hevs', // multiview sequence
        ];

        if (in_array($magicNumber, $heicMagicNumbers)) {
            return true;
        }

        return false;
    }

    private static function stringContains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
}
