<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Validator;

use Cron\CronExpression;
use Setono\CronExpressionBundle\Validator\CronExpression as Constraint;
use Setono\CronExpressionBundle\Validator\CronExpressionValidator;
use stdClass;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CronExpressionTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): CronExpressionValidator
    {
        return new CronExpressionValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Constraint());
        $this->assertNoViolation();
    }

    public function testEmptyIsValid(): void
    {
        $this->validator->validate('', new Constraint());
        $this->assertNoViolation();
    }

    public function testCronObjectIsValid(): void
    {
        $value = CronExpression::factory('* * * * *');
        $this->validator->validate($value, new Constraint());
        $this->assertNoViolation();
    }

    public function testCronStringIsValid(): void
    {
        $this->validator->validate('* * * * *', new Constraint());
        $this->assertNoViolation();
    }

    public function testInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('* * * * *', new NotNull());
    }

    public function testExpectsStringCompatibleValue(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new stdClass(), new Constraint());
    }

    /**
     * @dataProvider getInvalidValues
     *
     * @param mixed $value
     */
    public function testInvalidValues($value, string $valueAsString): void
    {
        $constraint = new Constraint('myMessage');

        $this->validator->validate($value, $constraint);

        /** @psalm-suppress InternalMethod,MixedMethodCall */
        $this->buildViolation('myMessage')->setParameter('{{ value }}', $valueAsString)->assertRaised();
    }

    /**
     * @psalm-return list<array{0: int|string, 1: string}>
     */
    public function getInvalidValues(): array
    {
        return [
            [123456, '"123456"'],
            ['*', '"*"'],
            ['* * * * * *', '"* * * * * *"'],
            ['61 * * * *', '"61 * * * *"'],
            ['-1 * * * *', '"-1 * * * *"'],
        ];
    }
}
