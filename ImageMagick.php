<?php

namespace EB\ImageMagickBundle;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
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
     * @var Filesystem
     */
    private $fs;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string          $command ImageMagick convert command on this specific machine
     * @param Filesystem      $fs      Filesystem
     * @param LoggerInterface $logger  Logger
     */
    public function __construct($command, Filesystem $fs, LoggerInterface $logger = null)
    {
        $this->command = $command;
        $this->fs = $fs;
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
        if ($which->isSuccessful()) {
            return trim($which->getOutput());
        }

        return null;
    }

    /**
     * @param File   $file       Source image
     * @param string $targetFile Target file path
     *
     * @return File|File[]|null
     */
    public function convert(File $file, $targetFile)
    {
        return $this->doConvert($file, $targetFile);
    }

    /**
     * @param File   $file       Source image
     * @param string $targetFile Target file path
     *
     * @return bool
     */
    public function convertAsync(File $file, $targetFile)
    {
        return $this->doConvert($file, $targetFile, array(), true);
    }

    /**
     * @param File[] $files      Source images
     * @param string $targetFile Target file path
     * @param float  $delay      Seconds between two frames
     * @param int    $loop       Wether this animation has to loop and how many times (0 will loop infinitly)
     *
     * @return File|null
     */
    public function generateAnimatedGif(array $files, $targetFile, $delay = 0.1, $loop = 0)
    {
        return $this->doConvert($files, $targetFile, array(
            'delay' => $delay,
            'loop' => $loop,
        ));
    }

    /**
     * @param File[] $files      Source images
     * @param string $targetFile Target file path
     * @param float  $delay      Seconds between two frames
     * @param int    $loop       Wether this animation has to loop and how many times (0 will loop infinitly)
     *
     * @return bool
     */
    public function generateAnimatedGifAsync(array $files, $targetFile, $delay = 0.1, $loop = 0)
    {
        return $this->doConvert($files, $targetFile, array(
            'delay' => $delay,
            'loop' => $loop,
        ), true);
    }

    /**
     * @param File|File[] $sources    Source images
     * @param string      $targetFile Target file path
     * @param array       $options    Convert command options
     * @param bool        $async      Wether this process has to be done asynchronously
     *
     * @return bool|null|File|File[]
     */
    private function doConvert($sources, $targetFile, array $options = array(), $async = false)
    {
        // Multiple files in entry
        if (!is_array($sources)) {
            $sources = array($sources);
        }

        /** @var File[] $files */
        $files = array_filter(array_map(function ($file) {
            if ($file instanceof File && $file->isReadable()) {
                return $file;
            }

            return null;
        }, $sources));

        // Prepare the target directory
        $this->fs->mkdir(pathinfo($targetFile, PATHINFO_DIRNAME));

        // Prepare command
        $args = array();
        $args[] = $this->command;
        foreach ($files as $file) {
            $args[] = sprintf('"%s"', $file->getRealPath());
        }
        foreach ($options as $key => $value) {
            $args[] = sprintf(' -%s %s', $key, $value);
        }
        $args[] = sprintf('"%s"', $targetFile);
        if ($async) {
            $args[] = '> /dev/null 2>/dev/null &';
        }
        $command = implode(' ', $args);
        $this->debug('Command %s', $command);

        // Launch process
        $process = new Process($command);
        $process->run();

        // Return async result
        if ($async) {
            return $process->isSuccessful();
        }

        // No actual result
        if (false === $process->isSuccessful()) {
            return null;
        }

        // One to One
        if (file_exists($targetFile)) {
            return new File($targetFile);
        }

        // One to Many
        $infos = pathinfo($targetFile);
        $pattern = sprintf('%s/%s-*.%s', $infos['dirname'], $infos['filename'], $infos['extension']);

        return array_map(function ($filePath) {
            return new File($filePath);
        }, glob($pattern));
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
