<?php

namespace EB\ImageMagickBundle\Tests;

use EB\ImageMagickBundle\ImageMagick;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Process;

/**
 * Class ImageMagickTest
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ImageMagickTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ImageMagick
     */
    private static $imageMagick;

    /**
     * Setup
     */
    public static function setUpBeforeClass()
    {
        if (null === $convertCommand = ImageMagick::getLocalCommand()) {
            self::markTestSkipped('No convert command found on this machine.');
        }
        self::$imageMagick = new ImageMagick($convertCommand, null);
    }

    /**
     * @param File       $source
     * @param string     $format
     *
     * @dataProvider getOneToOneConvertData
     */
    public function testOneToOneConvert(File $source, $format)
    {
        $filePath = sprintf('%s/%s.%s', sys_get_temp_dir(), uniqid(), $format);
        $file = self::$imageMagick->convert($source, $filePath);
        $this->assertFile($file);
    }

    /**
     * @param File       $source
     * @param string     $format
     *
     * @dataProvider getOneToOneConvertData
     */
    public function testOneToOneConvertAsync(File $source, $format)
    {
        $filePath = sprintf('%s/%s.%s', sys_get_temp_dir(), uniqid(), $format);
        $result = self::$imageMagick->convertAsync($source, $filePath);
        $this->assertTrue($result);
        sleep(3);
        $this->assertFile(new File($filePath));
    }

    /**
     * @return array
     */
    public function getOneToOneConvertData()
    {
        return array(
            array($this->createJpg(), 'jpg'),
            array($this->createJpg(), 'png'),
            array($this->createJpg(), 'gif'),
            array($this->createJpg(), 'bmp'),
            array($this->createJpg(), 'pdf'),
            array($this->createPdf(), 'jpg'),
            array($this->createPdf(), 'png'),
            array($this->createPdf(), 'gif'),
            array($this->createPdf(), 'bmp'),
            array($this->createPdf(), 'pdf'),
            array($this->createPdf(2), 'gif'),
        );
    }

    /**
     * @param File       $source
     * @param string     $format
     *
     * @dataProvider getOneToManyConvertData
     */
    public function testOneToManyConvert(File $source, $format)
    {
        $filePath = sprintf('%s/%s.%s', sys_get_temp_dir(), uniqid(), $format);
        $files = self::$imageMagick->convert($source, $filePath);
        $this->assertTrue(is_array($files));
        array_map(array($this, 'assertFile'), $files);
    }

    /**
     * @param File       $source
     * @param string     $format
     *
     * @dataProvider getOneToManyConvertData
     */
    public function testOneToManyConvertAsync(File $source, $format)
    {
        $filePath = sprintf('%s/%s.%s', sys_get_temp_dir(), uniqid(), $format);
        $result = self::$imageMagick->convertAsync($source, $filePath);
        $this->assertTrue($result);
        sleep(3);

        $infos = pathinfo($filePath);
        $pattern = sprintf('%s/%s-*.%s', $infos['dirname'], $infos['filename'], $infos['extension']);
        array_map(function ($filePath) {
            $this->assertFile(new File($filePath));
        }, glob($pattern));
    }

    /**
     * @return array
     */
    public function getOneToManyConvertData()
    {
        return array(
            array($this->createPdf(2), 'jpg'),
            array($this->createPdf(2), 'png'),
            array($this->createPdf(2), 'bmp'),
        );
    }

    /**
     * @param array $sources
     * @param float $delay
     * @param int   $loop
     *
     * @dataProvider getGenerateData
     */
    public function testGenerateAnimatedGif(array $sources, $delay, $loop)
    {
        $filePath = sprintf('%s/%s.gif', sys_get_temp_dir(), uniqid());
        $file = self::$imageMagick->generateAnimatedGif($sources, $filePath, $delay, $loop);
        $this->assertFile($file);
    }

    /**
     * @param array $sources
     * @param float $delay
     * @param int   $loop
     *
     * @dataProvider getGenerateData
     */
    public function testGenerateAnimatedGifAsync(array $sources, $delay, $loop)
    {
        $filePath = sprintf('%s/%s.gif', sys_get_temp_dir(), uniqid());
        $result = self::$imageMagick->generateAnimatedGifAsync($sources, $filePath, $delay, $loop);
        $this->assertTrue($result);
        sleep(3);
        $this->assertFile(new File($filePath));
    }

    /**
     * @return array
     */
    public function getGenerateData()
    {
        return array(
            array(array($this->createJpg(), $this->createJpg(), $this->createJpg()), 0.1, 0),
            array(array($this->createJpg(), $this->createJpg(), $this->createJpg()), 0.2, 0),
            array(array($this->createJpg(), $this->createJpg(), $this->createJpg()), 0.1, 1),
            array(array($this->createJpg(), $this->createJpg(), $this->createJpg()), 0.2, 1),
        );
    }

    /**
     * @param mixed $file
     */
    private function assertFile($file)
    {
        $this->assertNotNull($file);
        $this->assertTrue(is_object($file));
        $this->assertTrue($file instanceof File);
        $this->assertFileExists($file->getRealPath());
        $this->assertTrue(is_readable($file->getRealPath()));
        $this->assertTrue(is_writable($file->getRealPath()));
        $this->assertTrue(unlink($file->getRealPath()));
    }

    /**
     * @return File
     */
    private function createJpg()
    {
        return new File(__DIR__ . '/../Resources/test/test.jpg');
    }

    /**
     * @param int $page
     *
     * @return File
     */
    private function createPdf($page = 1)
    {
        $page = $page > 1 ? 2 : 1;

        return new File(__DIR__ . '/../Resources/test/test-' . $page . '.pdf');
    }
}