<?php
namespace SlaxWeb\ImageProcessor\Config;

/**
 * Configuration for avatars
 *
 * @author Tomaz Lovrec <tomaz.lovrec@gmail.com>
 */
class Image
{
	/**
	 * Image save path
	 *
	 * @var string
	 */
	protected $_path = "";

	public function __set($name, $value)
	{
		$name = "_{$name}";
		if (property_exists($this, $name) === true) {
			$this->{$name} = $value;
		}
	}

	public function __get($name)
	{
		$name = "_{$name}";
		if (property_exists($this, $name) === true) {
			return $this->{$name};
		}
	}

	public function path($path)
	{
		$this->_path = rtrim($path, "/") . "/";
	}
}