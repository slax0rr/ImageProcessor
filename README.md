ImageProcessor
==============

Image manipulation using PHP ImageMagick. Functionality is added as is needed. More of just a wrapper around ImageMagick for easier use.

How to use
==========

Initialization
--------------

To initialize the Image class, the config class must be initialized, which only holds the image save path at the moment. When initialized, it needs to be passed as the parameter to the Image class constructor. If using composer, just include the autoloader, if not, include the classes in your scripts.
```php
$config = new \SlaxWeb\ImageProcessor\ConfigImage();
$config->path("/path/to/images");
// or $config->path = "/path/to/images/";
$image = new \SlaxWeb\ImageProcessor\Image($config);
```

If you did not include the config at initialization, you can call it later, or if you want to change the config.
```php
$image->setConfig($config);
```

Loading the image
-----------------
You can either load the image from a file:
```php
$image->loadImage("imagename.jpg");
```
Or from a base64 string:
```php
$image->loadImageBase64($base64String);
```

To be continued...
