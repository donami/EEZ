<?php
class Image {

	private $image;
	private $fullPath;
	private $mime;
	private $extension;
	private $cacheFileName = null;
	private $quality = 100;
	private $width;
	private $height;
	private $newWidth;
	private $newHeight;
	private $allowedTypes = array();
	private $ignoreCache = false;
	private $saveAsExtension;
	private $errors = array();

	const IMAGE_PATH = 'public/img/';
	const CACHE_PATH = 'public/cache/';

	public function __construct($image, $data = array())
	{
		// If there is any errors we display them and kill the page
		if (!$this->validation())
		{
			// Loop through the errors and display them
			foreach ($this->errors as $error) {
				echo '<div>'.$error.'</div>';
			}

			die();
		}

		// Set the full path to the image
		$this->fullPath = self::IMAGE_PATH . $image;

		// Collect image information
		$imageInfo = list($this->width, $this->height, $type, $attr) = getimagesize($this->fullPath);


		$pathInfo = pathinfo($image);
		$this->extension = $pathInfo['extension'];

		$this->ignoreCache 	= isset($data['ignoreCache'])? $data['ignoreCache'] : false;
		$this->newWidth 	= isset($data['newWidth'])? $data['newWidth'] : $this->width;
		$this->newHeight 	= isset($data['newHeight'])? $data['newHeight'] : $this->height;
		$this->saveAsExtension = isset($data['saveAsExtension'])? $data['saveAsExtension'] : $this->extension;


		$this->allowedTypes = array('png', 'jpg', 'jpeg');

		switch ($this->extension) 
		{
			case 'png':

				$this->image = imagecreatefrompng($this->fullPath);
				break;
			
			case 'jpeg':
			case 'jpg':

				$this->image = imagecreatefromjpeg($this->fullPath);

				break;

			default:
				die("Not a valid filetype");
				break;
		}

		// Make sure that the file actually exists
		if (!is_file($this->fullPath))
			die("Image file does not exist!");




		// Set the mime type
		$this->mime = $imageInfo['mime'];


		$this->saveAs($this->saveAsExtension);

		// Fetch the cache filename
		$this->getCacheName();

	}

	/**
	 * Resize and image
	 *
	 * @param int $newWidth
	 * @param int $newHeight 
	 * @param bool $aspectRatio
	 * @return void
	 */
	public function resize($newWidth = null, $newHeight = null, $aspectRatio = false)
	{
		$newWidth = !is_null($newWidth)? $newWidth : $this->width;
		$newHeight = !is_null($newHeight)? $newHeight : $this->height;


		if ($aspectRatio == true)
		{
			$size = $this->calculateAspectRatio($newWidth, $newHeight);
			$newWidth = $size['width'];
			$newHeight = $size['height'];
		}

		// Create a new image with the new size
		$imageRezised = imagecreatetruecolor($newWidth, $newHeight);

		// Copy the original image to the new one 
		imagecopyresampled($imageRezised, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);

		// Set the current image to the new one
		$this->image = $imageRezised;


		// And assign the new sizes
		$this->width = $newWidth;
		$this->height = $newHeight;
	}

	/**
	 * Crop an image
	 *
	 * @param int $newWidth
	 * @param int $newHeight
	 * @return void
	 */
	public function crop($newWidth, $newHeight)
	{
		if (is_null($newHeight))
			die("You need both a width and height to crop an image");

		$aspectRatio = $this->width / $this->height;

		$targetRatio = $newWidth / $newHeight;
		$cropWidth   = $targetRatio > $aspectRatio ? $this->width : round($this->height * $targetRatio);
		$cropHeight  = $targetRatio > $aspectRatio ? round($this->width  / $targetRatio) : $this->height;

		$cropX = round(($this->width - $cropWidth) / 2);  
		$cropY = round(($this->height - $cropHeight) / 2);    
		$imageResized = imagecreatetruecolor($newWidth, $newHeight);
		imagecopyresampled($imageResized, $this->image, 0, 0, $cropX, $cropY, $newWidth, $newHeight, $cropWidth, $cropHeight);
		$this->image = $imageResized;
		$this->width = $newWidth;
		$this->height = $newHeight;
	}


	/**
	 * Sharpen the quality of the image
	 *
	 * @return void
	 */
	public function sharpen()
	{
		$matrix = array(
				array(-1,-1,-1,),
				array(-1,16,-1,),
				array(-1,-1,-1,)
			);

		$divisor = 8;
		$offset = 0;

		imageconvolution($this->image, $matrix, $divisor, $offset);
	}

	/**
	 * Calculate the aspect ratio from given width and height
	 *
	 * @param int $newWidth
	 * @param int $newHeight
	 * @return array
	 */
	private function calculateAspectRatio($newWidth = null, $newHeight = null)
	{
		$aspectRatio = $this->width / $this->height;

		// If only width is set
		if ($newWidth && !$newHeight) 
		{
			$newHeight = round($newWidth / $aspectRatio);
		}
		else if (!$newWidth && $newHeight)	// If only the height is specified 
		{
			$newWidth = round($newHeight * $aspectRatio);
		}
		else if($newWidth && $newHeight) 	// If both width and height is specified
		{
			$ratioWidth  = $this->width  / $newWidth;
			$ratioHeight = $this->height / $newHeight;
			$ratio = ($ratioWidth > $ratioHeight) ? $ratioWidth : $ratioHeight;
			$newWidth  = round($this->width  / $ratio);
			$newHeight = round($this->height / $ratio);
		}
		else 								// If neither is specified we keep the old values
		{
			$newWidth = $width;
			$newHeight = $height;
		}

		$sizes = ['width' => $newWidth, 'height' => $newHeight];

		return $sizes;
	}

	/**
	 * Save the file to specified location
	 *
	 * @return void
	 */
	public function saveAs($fileType)
	{
		if (!in_array($fileType, $this->allowedTypes))
			die("Not a valid image type");


		$this->saveAsExtension = $fileType;


		switch ($fileType) {
			case 'png':
				imagepng($this->image, $this->getCacheName());
				break;
			
			case 'jpeg':
			case 'jpg':
				imagejpeg($this->image, $this->getCacheName(), $this->quality);
				break;
		}

	
	}

	/**
	 * Validate all rules
	 *
	 * @return boolean
	 */
	private function validation()
	{
		if (!is_writable(self::CACHE_PATH)) $this->errors[] = "The cache directory is not writable";					// If the cache directory is not writable
		if (!is_dir(self::IMAGE_PATH)) 		$this->errors[] = "The image directory is not a valid directory"; 			// If the image directory is a valid directory

		if (!empty($this->errors))
			return false;

		return true;
	}

	/**
	 * Get the cached file name
	 *
	 * @return string
	 */
	public function getCacheName()
	{
		$pathInfo = pathinfo($this->fullPath);
		$quality = $this->quality;
		$dirName = str_replace('/', '-', $this->fullPath);
		$width = $this->width;
		$height = $this->height;

		$extension = isset($this->saveAsExtension)? $this->saveAsExtension : $this->extension;

		$cacheFileName = self::CACHE_PATH . "-{$dirName}-{$pathInfo['filename']}_{$width}_{$height}{$quality}.{$extension}";
		$cacheFileName = preg_replace('/^a-zA-Z0-9\.-_/', '', $cacheFileName);

		$this->cacheFileName = $cacheFileName;


		return $cacheFileName;
	}

	/**
	 * Set the quality 
	 *
	 * @return void
	 */
	public function setQuality($quality)
	{
		$this->quality = $quality;
	}

	/**
	 * Render the image
	 *
	 * @return void
	 */
	public function render()
	{
		// Get the modified time of the image file
		$imageModifiedTime = filemtime($this->fullPath);

		// If there is a cached version, get the modified time of that file
		$cacheModifiedTime = is_file($this->cacheFileName) ? filemtime($this->cacheFileName) : null;

		$gmdate = gmdate("D, d M Y H:i:s", $cacheModifiedTime);

		// If cached image is valid, output it.
		if (!$this->ignoreCache && is_file($this->cacheFileName) && $imageModifiedTime < $cacheModifiedTime) 
		{
			$info = getimagesize($this->cacheFileName);
			$mime = $info['mime'];


			header('Last-Modified: ' . $gmdate . ' GMT');

			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $cacheModifiedTime)
			{
				 header('HTTP/1.0 304 Not Modified');
			}

			header('Content-type: ' . $mime);  
			readfile($this->cacheFileName);		
		}
		elseif (is_file($this->cacheFileName)) {

			header('Content-type: ' . $this->mime);  
			readfile($this->cacheFileName);		
		}
		else
		{	
			header('Content-type: ' . $this->mime);  
			readfile($this->fullPath);		
		}

	}
}