<?php

namespace EB\ImageMagickBundle;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class ImageMagick
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ImageMagick
{
    /**
     * @var string
     */
    private $command;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string          $command ImageMagick convert command on this specific machine
     * @param LoggerInterface $logger  Logger
     */
    public function __construct($command, LoggerInterface $logger = null)
    {
        $this->command = $command;
        $this->logger = $logger;
    }

    /**
     * Get ImageMagick command on this specific machine
     *
     * @return null|string
     */
    public static function getLocalCommand()
    {
        $which = new Process('which "convert"');
        $which->run();
        if ('' == $convertCommand = trim($which->getOutput())) {
            return null;
        }

        return $convertCommand;
    }

    /**
     * @param Image       $image      Source image
     * @param string      $targetFile Target file path
     *
     * @return Image|null
     */
    public function convert(Image $image, $targetFile)
    {
        return $this->doConvert($image, $targetFile);
    }

    /**
     * @param Image       $image      Source image
     * @param string      $targetFile Target file path
     *
     * @return bool
     */
    public function convertAsync(Image $image, $targetFile)
    {
        return $this->doConvert($image, $targetFile, true);
    }

    /**
     * @param Image[]       $images     Source images
     * @param string        $targetFile Target file path
     * @param float         $second     Seconds between two frames
     * @param int           $loop       Wether this animation has to loop and how many times (0 will loop infinitly)
     *
     * @return Image|null
     */
    public function generateAnimatedGif(array $images, $targetFile, $second = 0.1, $loop = 0)
    {
        return $this->doGenerateAnimatedGif($images, $targetFile, $second, $loop);
    }

    /**
     * @param Image[]       $images     Source images
     * @param string        $targetFile Target file path
     * @param float         $second     Seconds between two frames
     * @param int           $loop       Wether this animation has to loop and how many times (0 will loop infinitly)
     *
     * @return bool
     */
    public function generateAnimatedGifAsync(array $images, $targetFile, $second = 0.1, $loop = 0)
    {
        return $this->doGenerateAnimatedGif($images, $targetFile, $second, $loop, true);
    }

    /**
     * @param Image       $image      Source image
     * @param string      $targetFile Target file path
     * @param bool        $async      Wether this process has to be done asynchronously
     *
     * @return bool|Image|null
     */
    private function doConvert(Image $image, $targetFile, $async = false)
    {
        // Prepare the target directory
        $fs = new Filesystem();
        $fs->mkdir(pathinfo($targetFile, PATHINFO_DIRNAME));

        // Prepare command
        $command = sprintf(
            $async ? '%s "%s" "%s" > /dev/null 2>/dev/null &' : '%s "%s" "%s"',
            $this->command,
            $image->getRealPath(),
            $targetFile
        );
        $this->debug('Command %s', $command);

        // Launch process
        $process = new Process($command);
        $process->run();

        // Return appropriate result
        if ($async) {
            return $process->isSuccessful();
        }

        return $process->isSuccessful() ? new Image($targetFile, true) : null;
    }

    /**
     * @param Image[]         $images     Source images
     * @param string          $targetFile Target file path
     * @param float           $second     Seconds between two frames
     * @param int             $loop       Wether this animation has to loop and how many times (0 will loop infinitly)
     * @param bool            $async      Wether this process has to be done asynchronously
     *
     * @return bool|Image|null
     */
    private function doGenerateAnimatedGif(array $images, $targetFile, $second = 0.1, $loop = 0, $async = false)
    {
        // Clean entry data
        $cleanedImages = array_filter(array_map(function ($image) {
            if ($image instanceof Image && $image->isReadable()) {
                return $image;
            }

            return null;
        }, $images));

        // Ensure entry images are valid
        if (0 === count($images) || count($images) !== count($cleanedImages)) {
            return $async ? false : null;
        }

        // Prepare the target directory
        $fs = new Filesystem();
        $fs->mkdir(pathinfo($targetFile, PATHINFO_DIRNAME));

        // Prepare command
        $command = sprintf('%s -delay %F -loop %u', $this->command, $second, $loop);
        foreach ($images as $image) {
            $command .= sprintf(' "%s"', $image->getRealPath());
        }
        $command .= sprintf(' "%s"', $targetFile);
        if ($async) {
            $command .= ' > /dev/null 2>/dev/null &';
        }
        $this->debug('Command %s', $command);

        // Launch process
        $process = new Process($command);
        $process->run();

        // Return appropriate result
        if ($async) {
            return $process->isSuccessful();
        }

        return $process->isSuccessful() ? new Image($targetFile, true) : null;
    }

    /**
     * Log
     */
    private function debug()
    {
        if ($this->logger) {
            $this->logger->debug(__CLASS__ . ' > ' . call_user_func_array('sprintf', func_get_args()));
        }
    }
}