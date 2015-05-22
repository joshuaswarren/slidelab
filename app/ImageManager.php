<?php
namespace App;

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


}