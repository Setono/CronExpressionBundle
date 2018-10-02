<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\DependencyInjection;

use Setono\CronExpressionBundle\Doctrine\DBAL\Types\CronExpressionType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class SetonoCronExpressionExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
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
            'dbal' => [
                'types' => [
                    'cron_expression' => CronExpressionType::class,
                ],
            ],
        ]);
    }
}
