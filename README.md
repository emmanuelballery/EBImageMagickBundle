EBImageMagickBundle
===================

Linux only ?

``` php
<?php
// SomeController.php

use EB\ImageMagickBundle\ImageMagick;
use Symfony\Component\HttpFoundation\File\File;

$imageMagick = $this->get('eb_imagemagick.imagemagick');
$bmpFile = new File('/path/to/a/valid/bmp');
$pdfFile = new File('/path/to/a/valid/pdf');

// Convert and wait for a resulting file
$pngImage = $imageMagick->convert($bmpFile, '/path/to/a/non/existing/png/file');
$bmpImage = $imageMagick->convert($bmpFile, '/path/to/a/non/existing/bmp/file');
$pdfImage = $imageMagick->convert($bmpFile, '/path/to/a/non/existing/pdf/file');

// Convert but don't wait
$imageMagick->convertAsync($bmpFile, '/path/to/a/non/existing/png/file');
$imageMagick->convertAsync($bmpFile, '/path/to/a/non/existing/bmp/file');
$imageMagick->convertAsync($bmpFile, '/path/to/a/non/existing/pdf/file');

// Export a PDF in multiple images
$jpgImages = $imageMagick->convert($pdfFile, '/path/to/a/non/existing/jpg/file');
$pngImages = $imageMagick->convert($pdfFile, '/path/to/a/non/existing/png/file');

// Export a PDF in one image
$gifImage = $imageMagick->convert($pdfFile, '/path/to/a/non/existing/gif/file');

// Create an animated GIF with a delay
// of 0.1 second which will loop 2 times
// and wait for a resulting image
$animatedGifImage = $imageMagick->generateAnimatedGif(array(
    $bmpFile,
    $bmpFile,
    $bmpFile,
    $bmpFile,
), '/path/to/a/non/existing/gif/image', 0.1, 2);

// Create an animated GIF with a delay
// of 0.1 second which will loop 2 times
// but don't wait
$imageMagick->generateAnimatedGifAsync(array(
    $bmpFile,
    $bmpFile,
    $bmpFile,
    $bmpFile,
), '/path/to/a/non/existing/gif/image', 0.1, 2);
```
