<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\DataTransformer;

use Cron\CronExpression;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @template-implements DataTransformerInterface<CronExpression, array<string,string>>
 *
 * @psalm-suppress TooManyTemplateParams
 */
final class CronExpressionToStringPartsTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return array<string,string>
     */
    #[\Override]
    public function transform($value): array
    {
        if (null === $value) {
            return [
                'minutes' => '*',
                'hours' => '*',
                'days' => '*',
                'months' => '*',
                'weekdays' => '*',
            ];
        }

        if (!$value instanceof CronExpression) {
            throw new TransformationFailedException('Expected an instance of ' . CronExpression::class);
        }

        return [
            'minutes' => (string) $value->getExpression((string) CronExpression::MINUTE),
            'hours' => (string) $value->getExpression((string) CronExpression::HOUR),
            'days' => (string) $value->getExpression((string) CronExpression::DAY),
            'months' => (string) $value->getExpression((string) CronExpression::MONTH),
            'weekdays' => (string) $value->getExpression((string) CronExpression::WEEKDAY),
        ];
    }

    /**
     * @param mixed $value
     */
    #[\Override]
    public function reverseTransform($value): CronExpression
    {
        $exception = new TransformationFailedException('Expected an instance of array{minutes: string, hours: string, days: string, months: string, weekdays: string}');

        $cronExpression = CronExpression::factory('* * * * *');

        if (null === $value) {
            return $cronExpression;
        }

        if (!is_array($value)) {
            throw $exception;
        }

        if (!isset($value['minutes'], $value['hours'], $value['days'], $value['months'], $value['weekdays'])) {
            throw $exception;
        }

        if (!self::allString($value)) {
            throw $exception;
        }

        try {
            $cronExpression
                ->setPart(CronExpression::MINUTE, $value['minutes'])
                ->setPart(CronExpression::HOUR, $value['hours'])
                ->setPart(CronExpression::DAY, $value['days'])
                ->setPart(CronExpression::MONTH, $value['months'])
                ->setPart(CronExpression::WEEKDAY, $value['weekdays'])
            ;
        } catch (\InvalidArgumentException $e) {
            throw $exception;
        }

        return $cronExpression;
    }

    /**
     * @psalm-assert iterable<string> $value
     */
    private static function allString(array $value): bool
    {
        return array_all($value, fn (mixed $s) => is_string($s));
    }
}
