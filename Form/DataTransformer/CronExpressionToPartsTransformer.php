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
     * @return array
     */
    public function transform($cronExpression)
    {
        if(null === $cronExpression) {
            return [
                'minutes' => '*',
                'hours' => '*',
                'days' => '*',
                'months' => '*',
                'weekdays' => '*',
            ];
        }

        if (!$cronExpression instanceof CronExpression) {
            throw new TransformationFailedException('Expected an instance of '.CronExpression::class);
        }

        return [
            'minutes' => $cronExpression->getExpression((string) CronExpression::MINUTE),
            'hours' => $cronExpression->getExpression((string) CronExpression::HOUR),
            'days' => $cronExpression->getExpression((string) CronExpression::DAY),
            'months' => $cronExpression->getExpression((string) CronExpression::MONTH),
            'weekdays' => $cronExpression->getExpression((string) CronExpression::WEEKDAY),
        ];
    }

    /**
     * @param array|null $array
     * @return CronExpression
     */
    public function reverseTransform($array)
    {
        $cronExpression = CronExpression::factory('* * * * *');

        if(null === $array) {
            return $cronExpression;
        }

        $cronExpression
            ->setPart(CronExpression::MINUTE, $array['minutes'])
            ->setPart(CronExpression::HOUR, $array['hours'])
            ->setPart(CronExpression::DAY, $array['days'])
            ->setPart(CronExpression::MONTH, $array['months'])
            ->setPart(CronExpression::WEEKDAY, $array['weekdays'])
        ;

        return $cronExpression;
    }
}
