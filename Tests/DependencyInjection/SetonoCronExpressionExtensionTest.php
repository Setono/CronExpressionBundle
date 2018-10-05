<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\CronExpressionBundle\DependencyInjection\SetonoCronExpressionExtension;

final class SetonoCronExpressionExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [
            new SetonoCronExpressionExtension(),
        ];
    }

    /**
     * @test
     */
    public function loadServices(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('setono_cron_expression.form.type_guesser.cron_expression');
    }
}
