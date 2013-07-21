EBImageMagickBundle
===================

Linux only ?

``` php
<?php
// SomeController.php

use EB\ImageMagickBundle\ImageMagick;
use Symfony\Component\HttpFoundation\File\File;

$imageMagick = $this->get('eb_imagemagick.imagemagick');
$file = new File('/path/to/a/valid/image');

// Convert and wait for a resulting image
$pngImage = $imageMagick->convert($file, '/path/to/a/non/existing/png/file');
$bmpImage = $imageMagick->convert($file, '/path/to/a/non/existing/bmp/file');
$pdfImage = $imageMagick->convert($file, '/path/to/a/non/existing/pdf/file');

// Convert but don't wait
$imageMagick->convertAsync($file, '/path/to/a/non/existing/png/file');
$imageMagick->convertAsync($file, '/path/to/a/non/existing/bmp/file');
$imageMagick->convertAsync($file, '/path/to/a/non/existing/pdf/file');

// Create an animated GIF with a delay
// of 0.1 second which will loop 2 times
// and wait for a resulting image
$animatedGifImage = $imageMagick->generateAnimatedGif(array(
    $file,
    $file,
    $file,
    $file,
), '/path/to/a/non/existing/gif/image', 0.1, 2);

// Create an animated GIF with a delay
// of 0.1 second which will loop 2 times
// but don't wait
$imageMagick->generateAnimatedGifAsync(array(
    $file,
    $file,
    $file,
    $file,
), '/path/to/a/non/existing/gif/image', 0.1, 2);

// Export a PDF in images
$pdfFile = new File('/path/to/a/valid/pdf');
$jpgImages = $imageMagick->convert($pdfFile, '/path/to/a/non/existing/jpg/file');
$pdfImages = $imageMagick->convert($pdfFile, '/path/to/a/non/existing/pdf/file');
$gifImage = $imageMagick->convert($pdfFile, '/path/to/a/non/existing/gif/file');

```
