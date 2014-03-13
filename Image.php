<?php
namespace SlaxWeb\ImageProcessor;

use \Imagick;
use \Exception;
use \ImagickException;

/**
 * Main image processor
 *
 * Loads the image from file or base64 string, and saves to provided path
 *
 * @author Tomaz Lovrec <tomaz.lovrec@gmail.com>
 */
class Image
{
	/**
	 * Config
	 *
	 * @var object
	 */
	protected $_config = null;
	/**
	 * Image name
	 *
	 * @var string
	 */
	protected $_imageName = "";
	/**
	 * ImageMagick
	 * 
	 * @var object
	 */
	protected $_image = null;

	/**
	 * Load the configuration, check if the image dir is writable and init the
	 * imagick object.
	 *
	 * @param $config \SlaxWeb\ImageProcessor\Config\Image Config object
	 */
	public function __construct(
		\SlaxWeb\ImageProcessor\Config\Image $config = null
	) {
		if ($config !== null) {
			$this->setConfig($config);
		}

		// init ImageMagick
		$this->_image = new Imagick();
	}

	/**
	 * Load config int local property for easier access
	 *
	 * @param $config \SlaxWeb\ImageProcess\Config\Image Config object
	 */
	public function setConfig(
		\SlaxWeb\ImageProcessor\Config\Image $config
	) {
		$this->_config = $config;

		// check dir is writable
		if (is_writable($this->_config->path) === false) {
			$msg = "Image directory does not exists or is not writable";
			$code = 3001;
			throw new Exception($msg, $code);
		}
	}

	/**
	 * Load image from an existing file
	 *
	 * Loads the image from a file. If the file does not exist an exception is thrown.
	 *
	 * @param $filename string Filename of the image
	 */
	public function loadImage($filename)
	{
		// check file exists
		if (file_exists($this->_config->path . $filename) === false) {
			$msg = "Image with filename {$filename} does not exist " . 
				"in path <{$this->_config->path}>";
			$code = 3002;
			throw new Exception($msg, $code);
		}

		$this->_image->readImageFile($this->_config->path . $filename);
		$this->_imageName = $filename;
	}

	/**
	 * Load image from base64
	 *
	 * Load the image from the provided base64 string
	 *
	 * @param $base64 string Base64 string of the image
	 * @param $imageName string Image name
	 */
	public function loadImageBase64($base64)
	{
		$imageData = base64_decode($base64, $imageName = "");
		if ($imageData === false) {
			$msg = "Invalid base64 string, cannot create image.\nData: {$base64}";
			$code  = 3003;
			throw new Exception($msg, $code);
		}

		$this->_image->readImageBlob($imageData);
		if ($imageName !== "") {
			$this->_image->setImageFilename($this->_config->path . $imageName);
			$this->_imageName = $imageName;
		}
	}

	/**
	 * Save image to file
	 *
	 * Saves image to provided filename or the previously set filename if none is
	 * passed in at this point.
	 *
	 * @param string $fileName Image filename
	 */
	public function saveImage($fileName = null)
	{
		return $this->_image->writeImage($this->_config->path . $fileName);
	}

	/**
	 * Resize image
	 *
	 * Resize image to provided dimensions. If height is null, width is used for
	 * both, if height is 0, then the resize is proportional. Saves the resized
	 * image to the provided filename, and reloads the original, unless filename
	 * is empty then the image is not saved and the resized image is stored in
	 * the class object.
	 *
	 * @param $size array Size of the image, must contain at least width,
	 * 						if height is not set then width is used for height,
	 * 						if height is 0, then the resizing is proportional
	 * @param $height int Height in px
	 */
	public function resizeImage(array $size, $blur = 1, $filename = "")
	{
		if (isset($size["height"]) === false) {
			$size["height"] = $size["width"];
		}
		$image = $this->_image;
		$status = $this->_image->resizeImage(
			$size["width"],
			$size["height"],
			Imagick::FILTER_LANCZOS,
			$blur
		);
		// no need to save the image, just return the resizing status
		if ($filename === "") {
			return $status;
		}
		// resizing successfull, save image to provided filename
		if ($status === true) {
			$this->saveImage($filename);
			$this->_image = $image;
			unset($image);
			return true;
		} else {
			// error at resizing
			$msg = "Image could not be resized.";
			$code = 3004;
			throw new Exception($msg, $code);
		}
	}

	/**
	 * Crop image
	 *
	 * Proportionaly crops the image, if the original image is 100x100 and crop size
	 * is 80x80, 10 pixels are removed on each side.
	 *
	 * @param $size array Size to which image must be cropped down to
	 */
	public function cropImage(array $size)
	{
		// calculate x and y for crop
		$x = ($this->_image->getImageWidth() - $size["width"]) / 2;
		$y = ($this->_image->getImageHeight() - $size["height"]) / 2;
		return $this->_image->cropImage($size["width"], $size["height"], $x, $y);
	}
}