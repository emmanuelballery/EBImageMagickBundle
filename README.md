EBImageMagickBundle
===================

Linux only ?

``` php
<?php
// SomeController.php

use EB\ImageMagickBundle\ImageMagick;
use Symfony\Component\HttpFoundation\File\File;

/** @var ImageMagick $imageMagick */
$imageMagick = $this->get('eb_imagemagick.imagemagick');
$bmpFile = new File('/path/to/an/existing/file.bmp');
$pdfFile = new File('/path/to/an/existing/file.pdf');

// Convert and wait for a resulting file
$pngImage = $imageMagick->convert($bmpFile, '/path/to/file.png');
$bmpImage = $imageMagick->convert($bmpFile, '/path/to/file.jpg');
$pdfImage = $imageMagick->convert($bmpFile, '/path/to/file.pdf');

// Convert but don't wait
$imageMagick->convertAsync($bmpFile, '/path/to/file.png');
$imageMagick->convertAsync($bmpFile, '/path/to/file.jpg');
$imageMagick->convertAsync($bmpFile, '/path/to/file.pdf');

// Export a PDF in multiple images
$pngImages = $imageMagick->convert($pdfFile, '/path/to/file.png');
$jpgImages = $imageMagick->convert($pdfFile, '/path/to/file.jpg');

// Export a PDF in one GIF image
$gifImage = $imageMagick->convert($pdfFile, '/path/to/file.gif');

// Create an animated GIF with a delay
// of 0.1 second which will loop 2 times
// and wait for a resulting image
$animatedGifImage = $imageMagick->generateAnimatedGif(array(
    $bmpFile,
    $bmpFile,
    $bmpFile,
    $bmpFile,
), '/path/to/image.gif', 0.1, 2);

// Create an animated GIF with a delay
// of 0.1 second which will loop 2 times
// but don't wait
$imageMagick->generateAnimatedGifAsync(array(
    $bmpFile,
    $bmpFile,
    $bmpFile,
    $bmpFile,
), '/path/to/image.gif', 0.1, 2);
```
