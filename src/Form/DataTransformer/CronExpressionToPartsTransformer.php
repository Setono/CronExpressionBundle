<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\DataTransformer;

use Cron\CronExpression;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Webmozart\Assert\Assert;

/**
 * @template-implements DataTransformerInterface<CronExpression, array<string, array<string>>>
 * @psalm-suppress TooManyTemplateParams
 */
final class CronExpressionToPartsTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return array<string, array<string>>
     */
    public function transform($value): array
    {
        if (null === $value) {
            return [
                'minutes' => ['*'],
                'hours' => ['*'],
                'days' => ['*'],
                'months' => ['*'],
                'weekdays' => ['*'],
            ];
        }

        if (!$value instanceof CronExpression) {
            throw new TransformationFailedException('Expected an instance of ' . CronExpression::class);
        }

        return [
            'minutes' => $this->convertCronString((string) $value->getExpression((string) CronExpression::MINUTE)),
            'hours' => $this->convertCronString((string) $value->getExpression((string) CronExpression::HOUR)),
            'days' => $this->convertCronString((string) $value->getExpression((string) CronExpression::DAY)),
            'months' => $this->convertCronString((string) $value->getExpression((string) CronExpression::MONTH)),
            'weekdays' => $this->convertCronString((string) $value->getExpression((string) CronExpression::WEEKDAY)),
        ];
    }

    /**
     * @param mixed $value
     */
    public function reverseTransform($value): CronExpression
    {
        $cronExpression = CronExpression::factory('* * * * *');

        if (null === $value) {
            return $cronExpression;
        }

        $exception = new TransformationFailedException('Expected an instance of array{minutes: array, hours: array, days: array, months: array, weekdays: array}');

        if (!is_array($value)) {
            throw $exception;
        }

        if (!isset($value['minutes'], $value['hours'], $value['days'], $value['months'], $value['weekdays'])) {
            throw $exception;
        }

        try {
            Assert::allIsArray($value);
        } catch (\InvalidArgumentException $e) {
            throw $exception;
        }

        try {
            $cronExpression
                ->setPart(CronExpression::MINUTE, $this->convertCronParts($value['minutes']))
                ->setPart(CronExpression::HOUR, $this->convertCronParts($value['hours']))
                ->setPart(CronExpression::DAY, $this->convertCronParts($value['days']))
                ->setPart(CronExpression::MONTH, $this->convertCronParts($value['months']))
                ->setPart(CronExpression::WEEKDAY, $this->convertCronParts($value['weekdays']))
            ;

            return $cronExpression;
        } catch (\InvalidArgumentException $e) {
            throw $exception;
        }
    }

    private function convertCronParts(array $cronArray): string
    {
        if ([] === $cronArray) {
            return '*';
        }

        Assert::allScalar($cronArray);

        return implode(',', $cronArray);
    }

    /**
     * @return array<string>
     */
    private function convertCronString(string $cronString): array
    {
        if ('*' === $cronString) {
            return [];
        }

        return explode(',', $cronString);
    }
}
