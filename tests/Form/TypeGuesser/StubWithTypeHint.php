<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\TypeGuesser;

use Cron\CronExpression;

final class StubWithTypeHint
{
    private ?CronExpression $property = null;
}
