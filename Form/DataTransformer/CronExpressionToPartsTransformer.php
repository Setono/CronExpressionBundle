<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\DataTransformer;

use Cron\CronExpression;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CronExpressionToPartsTransformer implements DataTransformerInterface
{
    /**
     * @param CronExpression|null $cronExpression
     *
     * @return array
     */
    public function transform($cronExpression)
    {
        if (null === $cronExpression) {
            return [
                'minutes' => ['*'],
                'hours' => ['*'],
                'days' => ['*'],
                'months' => ['*'],
                'weekdays' => ['*'],
            ];
        }

        if (!$cronExpression instanceof CronExpression) {
            throw new TransformationFailedException('Expected an instance of '.CronExpression::class);
        }

        return [
            'minutes' => $this->convertCronString($cronExpression->getExpression((string) CronExpression::MINUTE)),
            'hours' => $this->convertCronString($cronExpression->getExpression((string) CronExpression::HOUR)),
            'days' => $this->convertCronString($cronExpression->getExpression((string) CronExpression::DAY)),
            'months' => $this->convertCronString($cronExpression->getExpression((string) CronExpression::MONTH)),
            'weekdays' => $this->convertCronString($cronExpression->getExpression((string) CronExpression::WEEKDAY)),
        ];
    }

    /**
     * @param array|null $array
     *
     * @return CronExpression
     */
    public function reverseTransform($array)
    {
        $cronExpression = CronExpression::factory('* * * * *');

        if (null === $array) {
            return $cronExpression;
        }

        $cronExpression
            ->setPart(CronExpression::MINUTE, $this->convertCronParts($array['minutes']))
            ->setPart(CronExpression::HOUR, $this->convertCronParts($array['hours']))
            ->setPart(CronExpression::DAY, $this->convertCronParts($array['days']))
            ->setPart(CronExpression::MONTH, $this->convertCronParts($array['months']))
            ->setPart(CronExpression::WEEKDAY, $this->convertCronParts($array['weekdays']))
        ;

        return $cronExpression;
    }

    private function convertCronParts(array $cronArray): string
    {
        $cronString = join(',', $cronArray);

        return '' !== $cronString ? $cronString : '*';
    }

    private function convertCronString(string $cronString): array
    {
        if ('*' === $cronString) {
            return [];
        }

        return explode(',', $cronString);
    }
}
