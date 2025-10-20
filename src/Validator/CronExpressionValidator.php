<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Validator;

use Cron\CronExpression as DragonCronExpression;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class CronExpressionValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    #[\Override]
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CronExpression) {
            throw new UnexpectedTypeException($constraint, CronExpression::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        // if it is already a Cron Expression it is valid
        if ($value instanceof DragonCronExpression) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;
        if (!DragonCronExpression::isValidExpression($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation()
            ;
        }
    }
}
