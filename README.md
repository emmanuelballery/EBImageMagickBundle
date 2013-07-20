EBImageMagickBundle
===================

``` php
<?php
// SomeController.php

use EB\ImageMagickBundle\Image;

$imageMagick = $this->get('eb_imagemagick.imagemagick');
$image = new Image('/dir/to/a/valid/image');

// Convert and wait for a resulting image
$pngImage = $imageMagick->convert($image, '/dir/to/a/non/existing/png/image');
$bmpImage = $imageMagick->convert($image, '/dir/to/a/non/existing/bmp/image');

// Convert but don't wait
$imageMagick->convertAsync($image, '/dir/to/a/non/existing/png/image');
$imageMagick->convertAsync($image, '/dir/to/a/non/existing/bmp/image');

// Create an animated GIF with a delay of 0.1 sencond which will loop 2 times and wait for a resulting image
$animatedGifImage = $imageMagick->generateAnimatedGif(array(
    $image,
    $image,
    $image,
    $image,
), '/dir/to/a/non/existing/gif/image', 0.1, 2);

// Create an animated GIF with a delay of 0.1 sencond which will loop 2 times but don't wait
$animatedGifImage = $imageMagick->generateAnimatedGifAsync(array(
    $image,
    $image,
    $image,
    $image,
), '/dir/to/a/non/existing/gif/image', 0.1, 2);
```
