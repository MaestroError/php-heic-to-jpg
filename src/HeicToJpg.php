<?php

namespace maestroerror;

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
     * Takes full location of file as a string
     *
     * @param string $source
     */
    public function convertImage(string $source) {
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
     * Runs heicToJpg CLI tool to convert file
     *
     * @param string $source
     * @return void
     */
    protected function processImage(string $source) {
        $this->heic = $source;
        $newFileName = $source . "-" . uniqid(rand(), true);
        exec(__DIR__."/../bin/heicToJpg $source $newFileName", $output);
        foreach ($output as $line) {
            $parsed = $this->getStringBetween($line, '--', '--');
            if (!empty($parsed)) {
                $this->jpg = $parsed;
                break;
            }
        }
        if (empty($this->jpg)) {
            throw new \RuntimeException("Couldn't convert HEIC to JPG: " . implode("\\n", $output));
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

    public static function convert(string $source)
    {
        return (new self)->convertImage($source);
    }
}