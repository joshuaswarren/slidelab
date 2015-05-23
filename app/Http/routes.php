<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$siteroot = getenv('SITEROOT');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->get($siteroot, function() use ($app) {
    $siteroot = getenv('SITEROOT');
    $link = $siteroot . '/slide/new/';
    return "Welcome to Slidelab. <a href='$link'>Upload a slide to analyze.</a>";
});

$app->get($siteroot . '/test', function() use ($app) {
    return "Testing";
});

$app->get($siteroot . '/slide/new/', function() use ($app) {
    return view('uploader');
});

$app->post($siteroot . '/slide/new/', function() use ($app) {
    $siteroot = getenv('SITEROOT');
    $manager = new \App\ImageManager();
    $slide = Request::file('slide');
    $result = $manager->handleUpload($slide);
    if($result !== false) {
        return redirect($siteroot . '/slide/analyze/' . $result);
    }
    return "Slide failed to upload. Please try again.";
});

$app->get($siteroot . '/slide/view/{uuid}', function($uuid) use ($app) {
    $imageStoragePath = getenv('IMAGE_STORAGE_PATH');
    $cacheStoragePath = getenv('CACHE_STORAGE_PATH');
    $server = League\Glide\ServerFactory::create([
        'source' => new Filesystem(new Local($imageStoragePath)),
        'cache' => new Filesystem(new Local($cacheStoragePath)),
    ]);
    $server->outputImage(
        $uuid, $_GET
    );
});

$app->get($siteroot . '/slide/analyze/{uuid}', function($uuid) use ($app) {
    $siteroot = getenv('SITEROOT');
    $imagePath = $siteroot . '/slide/view/' . $uuid;
    $manager = new \App\ImageManager();
    $palette = $manager->colorPalette($uuid);
    $imageSize = $manager->imageSize($uuid);
    if (count($palette) > 1) {
        $primaryColor = $palette[0];
        $secondaryColor =  $palette[1];
        $colorDifference = $manager->colorDifference($primaryColor, $secondaryColor);
        $brightnessDifference = $manager->brightnessDifference($primaryColor, $secondaryColor);
        $luminosityContrast = $manager->luminosityContrast($primaryColor, $secondaryColor);
    } else {
        $colorDifference = null;
        $brightnessDifference = null;
        $luminosityContrast = null;
    }
    return view("analyzer", ['image' => $imagePath, 'palette' => $palette, 'colorDifference' => $colorDifference,
        'brightnessDifference' => $brightnessDifference, 'luminosityContrast' => $luminosityContrast,
        'imageSize' => $imageSize]);
});
