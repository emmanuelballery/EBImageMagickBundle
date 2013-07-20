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
     * @var File
     */
    private static $image;

    /**
     * @var File|null
     */
    private $target;

    /**
     * Setup
     */
    public static function setUpBeforeClass()
    {
        if (null === $convertCommand = ImageMagick::getLocalCommand()) {
            self::markTestSkipped('No convert command found on this machine.');
        }
        self::$imageMagick = new ImageMagick($convertCommand, null);
        self::$image = new File(__DIR__ . '/../Resources/test/images/test.jpg');
    }

    /**
     * Remove temp files
     */
    public function tearDown()
    {
        if (null !== $this->target && $this->target instanceof File) {
            if (null !== $path = $this->target->getRealPath()) {
                unlink($path);
            }
        }
    }

    /**
     * @dataProvider getConvertData
     */
    public function testConvert($format)
    {
        $this->target = self::$imageMagick->convert(self::$image, sprintf('%s/%s.%s', sys_get_temp_dir(), uniqid(), $format));
        $this->assertNotNull($this->target);
        $this->assertTrue($this->target instanceof File);
        $this->assertFileExists($this->target->getPath());
        $this->assertTrue(is_readable($this->target->getPath()));
    }

    /**
     * @dataProvider getConvertData
     */
    public function testConvertAsync($format)
    {
        $target = sprintf('%s/%s.%s', sys_get_temp_dir(), uniqid(), $format);
        $result = self::$imageMagick->convertAsync(self::$image, $target);
        $this->assertTrue($result);

        // Wait 5 seconds maximum
        $i = 0;
        while ($i++ < 5 && false === is_readable($target)) {
            sleep(1);
        }

        $this->assertFileExists($target);
        $this->assertTrue(is_readable($target));
        $this->target = new File($target);
    }

    public function testGenerateAnimatedGif()
    {
        $this->target = self::$imageMagick->generateAnimatedGif(array(
            self::$image,
            self::$image,
            self::$image,
            self::$image,
            self::$image,
        ), sprintf('%s/%s.gif', sys_get_temp_dir(), uniqid()));
        $this->assertNotNull($this->target);
        $this->assertTrue($this->target instanceof File);
        $this->assertFileExists($this->target->getPath());
        $this->assertTrue(is_readable($this->target->getPath()));
    }

    public function testGenerateAnimatedGifAsync()
    {
        $target = sprintf('%s/%s.gif', sys_get_temp_dir(), uniqid());
        $result = self::$imageMagick->generateAnimatedGifAsync(array(
            self::$image,
            self::$image,
            self::$image,
            self::$image,
            self::$image,
        ), $target);
        $this->assertTrue($result);

        // Wait 5 seconds maximum
        $i = 0;
        while ($i++ < 5 && false === is_readable($target)) {
            sleep(1);
        }

        $this->assertFileExists($target);
        $this->assertTrue(is_readable($target));
        $this->target = new File($target);
    }

    public function getConvertData()
    {
        return array(
            array('jpg'),
            array('png'),
            array('git'),
            array('bmp'),
        );
    }
}