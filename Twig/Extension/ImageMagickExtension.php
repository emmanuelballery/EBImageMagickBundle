<?php

namespace EB\ImageMagickBundle\Twig\Extension;

use EB\ImageMagickBundle\ImageMagick;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class ImageMagickExtension
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ImageMagickExtension extends \Twig_Extension
{
    /**
     * @var ImageMagick
     */
    private $imageMagick;

    /**
     * @param ImageMagick $imageMagick
     */
    public function __construct(ImageMagick $imageMagick)
    {
        $this->imageMagick = $imageMagick;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'eb_imagemagick.twig.extension.imagemagick';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'im_convert' => new \Twig_Function_Method($this, 'convert'),
            'im_convert_async' => new \Twig_Function_Method($this, 'convertAsync'),
            'im_generate_agif' => new \Twig_Function_Method($this, 'generateAnimatedGif'),
            'im_generate_agif_async' => new \Twig_Function_Method($this, 'generateAnimatedGifAsync'),
        );
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'im_convert' => new \Twig_Filter_Method($this, 'convert'),
            'im_convert_async' => new \Twig_Filter_Method($this, 'convertAsync'),
            'im_generate_agif' => new \Twig_Filter_Method($this, 'generateAnimatedGif'),
            'im_generate_agif_async' => new \Twig_Filter_Method($this, 'generateAnimatedGifAsync'),
        );
    }

    /**
     * @param string|File $file
     * @param string      $ext
     *
     * @return File
     */
    public function convert($file, $ext)
    {
        list($file, $targetFile) = $this->handleArgs($file, $ext);

        return $this->imageMagick->convert($file, $targetFile);
    }

    /**
     * @param string|File $file
     * @param string      $ext
     *
     * @return File
     */
    public function convertAsync($file, $ext)
    {
        list($file, $targetFile) = $this->handleArgs($file, $ext);

        return $this->imageMagick->convertAsync($file, $targetFile);
    }

    /**
     * @param string|File   $file
     * @param float         $delay
     * @param int           $loop
     *
     * @return File
     */
    public function generateAnimatedGif($file, $delay = 0.1, $loop = 0)
    {
        list($file, $targetFile) = $this->handleArgs($file, 'gif');

        return $this->imageMagick->generateAnimatedGif($file, $targetFile, $delay, $loop);
    }

    /**
     * @param string|File   $file
     * @param float         $delay
     * @param int           $loop
     *
     * @return File
     */
    public function generateAnimatedGifAsync($file, $delay = 0.1, $loop = 0)
    {
        list($file, $targetFile) = $this->handleArgs($file, 'gif');

        return $this->imageMagick->generateAnimatedGifAsync($file, $targetFile, $delay, $loop);
    }

    /**
     * @param string|File $file
     * @param string      $ext
     *
     * @return array
     */
    private function handleArgs($file, $ext)
    {
        if (is_string($file)) {
            $file = new File($file);
        }
        $infos = pathinfo($file->getRealPath());
        $targetFile = sprintf('%s/%s-*.%s', $infos['dirname'], $infos['filename'], $ext);

        return array($file, $targetFile);
    }
}