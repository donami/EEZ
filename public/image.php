<?php
include dirname(__DIR__) . '/classes/Image.php';
$source = isset($_GET['src'])? $_GET['src'] : null;					# Source to the imagefile
$saveAs = isset($_GET['save'])? $_GET['save'] : null;				# Save as filetype
$quality = isset($_GET['quality'])? (int)$_GET['quality'] : 100;	# Quality of the image
$ignoreCache = isset($_GET['no-cache'])? true : false;				# Will ignore cache if true
$newWidth 	= isset($_GET['width'])? (int)$_GET['width'] : null;	# New width of the image
$newHeight 	= isset($_GET['height'])? (int)$_GET['height'] : null;	# New height of the image
$aspectRatio  = isset($_GET['aspect-ratio'])? true : false;			# If true the image will keep the aspect ratio
$resize = isset($_GET['resize'])? true : false;						# Will resize if set to true
$cropImage 	= isset($_GET['crop'])? true : false;					# Will crop the image to fit the given size
$sharpen = isset($_GET['sharpen'])? true : false;					# Sharpen the image or not
// Data to pass along as parameter to the image class
$data = array(
		'ignoreCache' 	=> $ignoreCache,
		'newWidth'		=> $newWidth,
		'newHeight'		=> $newHeight,
		'saveAsExtension' => $saveAs
	);
// Create the image
$image = new Image($source, $data);
// Set the quality
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
// Render the image
$image->render();