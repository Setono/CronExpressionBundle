<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoCronExpressionExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('doctrine')) {
            throw new \Exception('The doctrine extension was not loaded');
        }

        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'SetonoCronExpressionBundle' => [
                        'type' => 'xml',
                        'prefix' => 'Cron',
                    ],
                ],
            ],
        ]);
    }
}
