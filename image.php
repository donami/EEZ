<?php
include __DIR__ . '/classes/Image.php';


error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly

$source = isset($_GET['src'])? $_GET['src'] : null;
$saveAs = isset($_GET['save'])? $_GET['save'] : null;
$quality = isset($_GET['quality'])? (int)$_GET['quality'] : 100;
$ignoreCache = isset($_GET['no-cache'])? true : false;

$newWidth 	= isset($_GET['width'])? (int)$_GET['width'] : null;
$newHeight 	= isset($_GET['height'])? (int)$_GET['height'] : null;
$aspectRatio  = isset($_GET['aspect-ratio'])? true : false;
$resize = isset($_GET['resize'])? true : false;
$cropImage 	= isset($_GET['crop'])? true : false;
$sharpen = isset($_GET['sharpen'])? true : false;

$data = array(
		'ignoreCache' 	=> $ignoreCache,
		'newWidth'		=> $newWidth,
		'newHeight'		=> $newHeight,
	);


$image = new Image($source, $data);
$image->setQuality($quality);

// If the resize variable is true we call the resize method
if ($resize)
{
	$image->resize($newWidth, $newHeight, $aspectRatio);
}

// If the cropImage variable is true we call the crop method
if ($cropImage)
{
	$image->crop($newWidth, $newHeight);
}

// If sharpen is true we call the method for sharpening the image
if ($sharpen)
{
	$image->sharpen();
}

if ($saveAs != null)
{
	$image->saveAs($saveAs);
}


$image->render();