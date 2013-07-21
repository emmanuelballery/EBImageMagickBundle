<?php

namespace EB\ImageMagickBundle\Twig\Extension;

use EB\ImageMagickBundle\ImageMagick;

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
            'IM_convert' => new \Twig_Function_Method($this->imageMagick, 'convert'),
            'IM_convert_async' => new \Twig_Function_Method($this->imageMagick, 'convertAsync'),
            'IM_generate_animated_gif' => new \Twig_Function_Method($this->imageMagick, 'generateAnimatedGif'),
            'IM_generate_animated_gif_async' => new \Twig_Function_Method($this->imageMagick, 'generateAnimatedGifAsync'),
        );
    }
}