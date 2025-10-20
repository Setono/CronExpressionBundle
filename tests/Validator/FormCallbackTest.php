<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Validator;

use Cron\CronExpression;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\CallbackValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @template-extends ConstraintValidatorTestCase<CallbackValidator>
 *
 * @psalm-suppress TooManyTemplateParams
 */
final class FormCallbackTest extends ConstraintValidatorTestCase
{
    #[\Override]
    protected function createValidator(): CallbackValidator
    {
        return new CallbackValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, $this->createConstraint(CronExpression::MINUTE));
        $this->assertNoViolation();
    }

    public function testValidIsValid(): void
    {
        $this->validator->validate('0', $this->createConstraint(CronExpression::MINUTE));
        $this->assertNoViolation();
    }

    public function testOutOfRangeIsNotValid(): void
    {
        $value = '61';
        $this->validator->validate($value, $this->createConstraint(CronExpression::MINUTE));
        /** @psalm-suppress InternalMethod,MixedMethodCall */
        $this->buildViolation('{{value}} is not a valid cron part')->setParameter('value', $value)->assertRaised();
    }

    protected function createConstraint(int $payload): Callback
    {
        // helper function for Symfony 4.4
        return new Callback([
            'callback' => [new CronExpressionType(), 'validateCronField'],
            'payload' => $payload,
        ]);
    }
}
