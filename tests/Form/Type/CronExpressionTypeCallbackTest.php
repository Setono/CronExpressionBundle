<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\Type;

use Cron\CronExpression;
use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CronExpressionTypeCallbackTest extends TestCase
{
    public function testNullNoViolation(): void
    {
        $this->callValidateCronField(null, false);
    }

    public function testValidNoViolation(): void
    {
        $this->callValidateCronField('59', false);
    }

    public function testViolationAdded(): void
    {
        $this->callValidateCronField('61', true);
    }

    protected function callValidateCronField(?string $value, bool $match): void
    {
        $mock = $this->createMock(ExecutionContextInterface::class);

        $mock->expects(
            $match ?
/** @phpstan-ignore staticMethod.dynamicCall */ $this->once() :
/** @phpstan-ignore staticMethod.dynamicCall */ $this->never(),
        )
            ->method('addViolation')
            ->with('{{value}} is not a valid cron part', ['value' => $value])
        ;

        $type = new CronExpressionType();
        $type->validateCronField($value, $mock, CronExpression::MINUTE);
    }
}
