<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\TypeGuesser;

use Cron\CronExpression;

final class StubWithTypeHint
{
    /** @phpstan-ignore property.unusedType,property.onlyWritten */
    private ?CronExpression $property = null;
}
