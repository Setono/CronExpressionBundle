<?php

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

    public function testNullIsValid()
    {
        $this->validator->validate(null, new Constraint());
        $this->assertNoViolation();
    }

    public function testEmptyIsValid()
    {
        $this->validator->validate('', new Constraint());
        $this->assertNoViolation();
    }

    public function testCronObjectIsValid()
    {
        $value = new CronExpression('* * * * *');
        $this->validator->validate($value, new Constraint());
        $this->assertNoViolation();
    }

    public function testCronStringIsValid()
    {
        $this->validator->validate('* * * * *', new Constraint());
        $this->assertNoViolation();
    }

    public function testInvalidConstraint()
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('* * * * *', new NotNull());
    }

    public function testExpectsStringCompatibleValue()
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new stdClass(), new Constraint());
    }

    /**
     * @dataProvider getInvalidValues
     * @param $value
     * @param $valueAsString
     * @return void
     */
    public function testInvalidValues($value, $valueAsString)
    {
        $constraint = new Constraint(['message' => 'myMessage']);

        $this->validator->validate($value, $constraint);

        $this->buildViolation('myMessage')->setParameter('{{ value }}', $valueAsString)->assertRaised();
    }

    public function getInvalidValues(): array
    {
        return [
            [123456, '"123456"'],
            ['*', '"*"'],
            ['* * * * * *', '"* * * * * *"'],
            ['61 * * * *', '"61 * * * *"'],
            ['-1 * * * *', '"-1 * * * *"']
        ];
    }
}
