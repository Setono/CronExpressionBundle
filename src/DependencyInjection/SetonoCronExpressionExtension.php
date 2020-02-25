<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\DependencyInjection;

use const E_USER_WARNING;
use Exception;
use Setono\CronExpressionBundle\Doctrine\DBAL\Types\CronExpressionType;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoCronExpressionExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @throws Exception
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * @throws Exception
     */
    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('doctrine')) {
            @trigger_error('The doctrine extension was not loaded. Install using `composer req doctrine/doctrine-bundle`', E_USER_WARNING);

            return;
        }

        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'types' => [
                    'cron_expression' => CronExpressionType::class,
                ],
            ],
        ]);
    }
}
