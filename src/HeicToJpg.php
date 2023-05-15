<?php

namespace Maestroerror;

class HeicToJpg {

    /**
     * Stores binary content of JPG file
     *
     */
    private $binary;

    /**
     * Stores converted JPG file location
     *
     * @var string
     */
    private string $jpg;

    /**
     * Stores original HEIC image path
     *
     * @var string
     */
    protected string $heic;

    /**
     * Executable file name from bin folder
     *
     * @var string
     */
    protected string $exeName = "heicToJpg";

    /**
     * OS of server
     *
     * @var string
     */
    protected string $os = "linux";

    /**
     * Architecture of server
     *
     * @var string
     */
    protected string $arch = "amd64";

    /**
     * Takes full location of file as a string
     *
     * @param string $source
     */
    public function convertImage(string $source) {
        $this->checkLinuxOS();
        $this->processImage($source);
        $this->extractBinary();
        return $this;
    }

    /**
     * The same as convertImage but for MacOS users
     *
     * @param string $source
     */
    public function convertImageMac(string $source, $arch = "amd64") {
        $this->setDarwinExe($arch);
        $this->processImage($source);
        $this->extractBinary();
        return $this;
    }

    /**
     * Saves JPG file as $path (Full location is preferable)
     *
     * @param string $path
     * @return bool
     */
    public function saveAs(string $path) {
        file_put_contents($path, $this->binary);
        return $this->exit();
    }

    /**
     * Removes temporary JPG file and returns it's content (binary)
     *
     * @return string
     */
    public function get() {
        $this->exit();
        return $this->binary;
    }

    /**
     * Checks is used on macOS or not
     *
     * @return self
     */
    public function checkMacOS(): self {
        $os = strtolower(php_uname('s'));
        $arch = strtolower(php_uname('m'));

        if (str_contains($os, 'macos') || str_contains($os, 'os x') || str_contains($os, 'darwin') || str_contains($os, 'macintosh')) {
            $this->os = "darwin";
        }

        if (str_contains($arch, "x86_64") || str_contains($arch, "amd64")) {
            $this->arch = "amd64";
        }

        if (str_contains($arch, "arm")) {
            $this->arch = "arm64";
        }

        $this->checkDarwinExe();

        return $this;
    }

    public function checkLinuxOS(): self {
        $os = strtolower(php_uname('s'));
        $arch = strtolower(php_uname('m'));

        if (str_contains($os, 'linux')) {
            $this->os = "linux";
        }

        if (str_contains($arch, "aarch64")){
            $this->arch = "arm64";
        }

        $this->checkLinuxExe();

        return $this;
    }

    /**
     * Runs heicToJpg CLI tool to convert file
     *
     * @param string $source
     * @return void
     */
    protected function processImage(string $source) {
        $this->heic = $source;
        $newFileName = $source . "-" . uniqid(rand(), true);
        $exeName = $this->exeName;
        $command = __DIR__.'/../bin/'.$exeName.' "'.$source.'" "'.$newFileName.'"';
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
            throw new \RuntimeException("Couldn't convert HEIC to JPG: '" . $error . "' | Bin used: '" . $this->exeName . "' HEIC: '" . $source . "' Full Command: '" . $command . "'");
        }
    }

    /**
     * Read the content of file
     *
     * @return void
     */
    protected function extractBinary() {
        $this->binary = file_get_contents($this->jpg);
    }

    /**
     * Returns string between $start and $end
     *
     * @param string $string
     * @param string $start
     * @param string $end
     * @return void
     */
    private function getStringBetween(string $string, string $start, string $end){
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
     * @return bool
     */
    private function exit() {
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

    /**
     * Check os and arch properties to set executable name correctly
     *
     * @return void
     */
    private function checkDarwinExe() {
        if ($this->os == "darwin" && $this->arch == "amd64") {
            $this->exeName = "php-heic-to-jpg-darwin-amd64";
        }
        if ($this->os == "darwin" && $this->arch == "arm64") {
            $this->exeName = "php-heic-to-jpg-darwin-arm64";
        }
    }

    /**
     * Sets macOS executable by architecture
     *
     * @param string $arch
     * @return void
     */
    private function setDarwinExe(string $arch) {
        if ($arch == "arm64") {
            $this->exeName = "php-heic-to-jpg-darwin-arm64";
        } else {
            $this->exeName = "php-heic-to-jpg-darwin-amd64";
        }
    }

    public static function convert(string $source)
    {
        return (new self)
            ->checkMacOS()
            ->checkLinuxOS()
            ->convertImage($source);
    }

    public static function convertOnMac(string $source, $arch = "amd64")
    {
        return (new self)->convertImageMac($source, $arch);
    }

    /**
     * Check if file is in HEIC format.
     *
     * @param string $path
     * @return bool
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

}
