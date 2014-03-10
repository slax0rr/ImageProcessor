<?php
namespace SlaxWeb\ImageProcess;

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
	 * @param $config \SlaxWeb\ImageProcess\Config\Image Config object
	 */
	public function __construct(
		\SlaxWeb\ImageProcess\Config\Image $config = null
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
		\SlaxWeb\ImageProcess\Config\Image $config
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
		$this->_imageName = $imageName;
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
			$msg = "Invalid base64 string, cannot create image.";
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
		return $this->_image->writeImage($fileName);
	}
}