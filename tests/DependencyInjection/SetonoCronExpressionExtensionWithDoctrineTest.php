<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\CronExpressionBundle\DependencyInjection\SetonoCronExpressionExtension;
use Setono\CronExpressionBundle\Doctrine\DBAL\Types\CronExpressionType;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class SetonoCronExpressionExtensionWithDoctrineTest extends AbstractExtensionTestCase
{
    #[\Override]
    protected function getContainerExtensions(): array
    {
        return [
            $this->createDoctrineExtensionMock(),
            new SetonoCronExpressionExtension(),
        ];
    }

    public function testLoadServices(): void
    {
        $this->setParameter('kernel.debug', true);
        $this->load();

        $this->assertContainerBuilderHasService('setono_cron_expression.form.type_guesser.cron_expression');
        $doctrineConfig = $this->container->getExtensionConfig('doctrine');

        self::assertEquals([
            [
                'dbal' => [
                    'types' => [
                        'cron_expression' => CronExpressionType::class,
                    ],
                ],
            ],
        ], $doctrineConfig);
    }

    protected function createDoctrineExtensionMock(): Extension
    {
        $doctrine = $this->createMock(Extension::class);
        $doctrine->method('getAlias')->willReturn('doctrine');

        return $doctrine;
    }
}
