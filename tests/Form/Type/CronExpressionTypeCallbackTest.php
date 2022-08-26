<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\Type;

use Cron\CronExpression;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CronExpressionTypeCallbackTest extends TestCase
{
    public function testNullNoViolation(): void
    {
        $this->callValidateCronField(null, $this->never());
    }

    public function testValidNoViolation(): void
    {
        $this->callValidateCronField('59', $this->never());
    }

    public function testViolationAdded(): void
    {
        $this->callValidateCronField('61', $this->once());
    }

    protected function callValidateCronField(?string $value, InvokedCountMatcher $counter): void
    {
        $mock = $this->createMock(ExecutionContextInterface::class);
        $mock->expects($counter)->method('addViolation')
            ->with('{{value}} is not a valid cron part', ['value' => $value])
        ;

        $type = new CronExpressionType();
        $type->validateCronField($value, $mock, CronExpression::MINUTE);
    }
}
