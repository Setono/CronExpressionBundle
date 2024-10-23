<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\DataTransformer;

use Cron\CronExpression;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @template-implements DataTransformerInterface<CronExpression, string>
 * @psalm-suppress TooManyTemplateParams
 */
final class CronExpressionToStringTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     */
    public function transform($value): ?string
    {
        if (null === $value) {
            return '* * * * *';
        }

        if (!$value instanceof CronExpression) {
            throw new TransformationFailedException('Expected an instance of ' . CronExpression::class);
        }

        return $value->getExpression();
    }

    /**
     * @param mixed $value
     */
    public function reverseTransform($value): CronExpression
    {
        if (null === $value || '' === $value) {
            return CronExpression::factory('* * * * *');
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected an instance of string');
        }

        try {
            return CronExpression::factory($value);
        } catch (\InvalidArgumentException $ex) {
            throw new TransformationFailedException('Invalid CronExpression', $ex->getCode(), $ex);
        }
    }
}
