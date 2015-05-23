<?php
namespace App;
use  League\ColorExtractor\Image;

if(file_exists('bootstrap/app.php')) {
    require_once('bootstrap/app.php');
} else {
    require_once('../bootstrap/app.php');
}

use Rhumsaa\Uuid\Uuid;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

class ImageManager
{

    private $_filesystem;
    private $_imageStoragePath;
    private $_cacheStoragePath;

    function __construct()
    {
        $this->_imageStoragePath = getenv('IMAGE_STORAGE_PATH');
        $this->_cacheStoragePath = getenv('CACHE_STORAGE_PATH');
        $this->_filesystem = new Filesystem(new Adapter($this->_imageStoragePath));
    }

    public function handleUpload($slide) {
        // TODO: Make sure users only upload a valid image file
        $uuid = Uuid::uuid1()->toString();
        $filename = $uuid;
        $fullfilename = '/tmp/' . $filename;
        $slide->move('/tmp', $uuid);
        $contents = file_get_contents($fullfilename);
        unlink($fullfilename);
        $result = $this->_filesystem->write($uuid, $contents);
        if($result === true) {
            return $uuid;
        } else {
            return false;
        }
    }

    public function colorPalette($uuid) {
        $imageContents = $this->_filesystem->read($uuid);
        $imageResource = imagecreatefromstring($imageContents);
        $image = new \League\ColorExtractor\Image($imageResource);
        $palette = $image->extract(3);
        return $palette;
    }

    public function imageSize($uuid) {
        $imageContents = $this->_filesystem->read($uuid);
        $imageSize = getimagesizefromstring($imageContents);
        return $imageSize;
    }

    /*
     * These three functions courtesy of http://www.splitbrain.org/blog/2008-09/18-calculating_color_contrast_with_php
     */

    public function coldiff($R1,$G1,$B1,$R2,$G2,$B2){
        return max($R1,$R2) - min($R1,$R2) +
        max($G1,$G2) - min($G1,$G2) +
        max($B1,$B2) - min($B1,$B2);
    }

    public function brghtdiff($R1,$G1,$B1,$R2,$G2,$B2){
        $BR1 = (299 * $R1 + 587 * $G1 + 114 * $B1) / 1000;
        $BR2 = (299 * $R2 + 587 * $G2 + 114 * $B2) / 1000;

        return abs($BR1-$BR2);
    }

    public function lumdiff($R1,$G1,$B1,$R2,$G2,$B2){
        $L1 = 0.2126 * pow($R1/255, 2.2) +
            0.7152 * pow($G1/255, 2.2) +
            0.0722 * pow($B1/255, 2.2);

        $L2 = 0.2126 * pow($R2/255, 2.2) +
            0.7152 * pow($G2/255, 2.2) +
            0.0722 * pow($B2/255, 2.2);

        if($L1 > $L2){
            return ($L1+0.05) / ($L2+0.05);
        }else{
            return ($L2+0.05) / ($L1+0.05);
        }
    }

    // result higher than 500 is recommended for readability

    public function colorDifference($hexColorOne, $hexColorTwo) {
        $rgbColorOne = $this->html2rgb($hexColorOne);
        $rgbColorTwo = $this->html2rgb($hexColorTwo);
        return round($this->coldiff($rgbColorOne[0], $rgbColorOne[1], $rgbColorOne[2],
            $rgbColorTwo[0], $rgbColorTwo[1], $rgbColorTwo[2]), 0);
    }

    // result higher than 125 is recommended for readability

    public function brightnessDifference($hexColorOne, $hexColorTwo) {
        $rgbColorOne = $this->html2rgb($hexColorOne);
        $rgbColorTwo = $this->html2rgb($hexColorTwo);
        return round($this->brghtdiff($rgbColorOne[0], $rgbColorOne[1], $rgbColorOne[2],
            $rgbColorTwo[0], $rgbColorTwo[1], $rgbColorTwo[2]), 0);
    }

    // result higher than 5 is recommended for readability

    public function luminosityContrast($hexColorOne, $hexColorTwo) {
        $rgbColorOne = $this->html2rgb($hexColorOne);
        $rgbColorTwo = $this->html2rgb($hexColorTwo);
        return round($this->lumdiff($rgbColorOne[0], $rgbColorOne[1], $rgbColorOne[2],
            $rgbColorTwo[0], $rgbColorTwo[1], $rgbColorTwo[2]), 2);
    }

    // From http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml

    protected function html2rgb($color)
    {
        if ($color[0] == '#')
            $color = substr($color, 1);

        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0].$color[1],
                $color[2].$color[3],
                $color[4].$color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        else
            return false;

        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

        return array($r, $g, $b);
    }

}