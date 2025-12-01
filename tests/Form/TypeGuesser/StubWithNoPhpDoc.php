<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\TypeGuesser;

final class StubWithNoPhpDoc
{
    /**
     * @phpstan-ignore property.unused,missingType.property
     */
    private $property;
}
