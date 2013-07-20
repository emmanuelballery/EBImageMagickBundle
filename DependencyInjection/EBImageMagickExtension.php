<?php

namespace EB\ImageMagickBundle\DependencyInjection;

use EB\ImageMagickBundle\ImageMagick;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Process\Process;

/**
 * Class EBImagImageMagickExtension
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EBImageMagickExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Search for "convert" command
        if (null === $convertCommand = ImageMagick::getLocalCommand()) {
            throw new InvalidConfigurationException('Local command "convert" not found.');
        }

        // Add it for our service to find
        $container->setParameter('eb_imagemagick.command.convert', $convertCommand);

        // Load rest of our bundle
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }
}