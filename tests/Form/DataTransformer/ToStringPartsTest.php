<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\DataTransformer;

use Cron\CronExpression;
use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Form\DataTransformer\CronExpressionToStringPartsTransformer;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ToStringPartsTest extends TestCase
{
    public function testNullReverseTransform(): void
    {
        $this->expectedReverseTransform(null, '* * * * *');
    }

    public function testArrayReverseTransform(): void
    {
        $this->expectedReverseTransform([
            'minutes' => '0',
            'hours' => '12',
            'days' => '1',
            'months' => '6',
            'weekdays' => '3',
        ], '0 12 1 6 3');
    }

    public function testWrongClassInvalidReverseTransform(): void
    {
        $this->invalidReverseTransform(new stdClass());
    }

    public function testMissingKeyInvalidReverseTransform(): void
    {
        $this->invalidReverseTransform([
            'minutes' => '*',
        ]);
    }

    public function testFaultyKeyInvalidReverseTransform(): void
    {
        $this->invalidReverseTransform([
            'minutes' => 0,
            'hours' => '12',
            'days' => '1',
            'months' => '6',
            'weekdays' => '3',
        ]);
    }

    /**
     * @param mixed $value
     */
    protected function invalidReverseTransform($value): void
    {
        $transformer = new CronExpressionToStringPartsTransformer();
        $this->expectException(TransformationFailedException::class);
        $transformer->reverseTransform($value);
    }

    /**
     * @param mixed $input
     */
    protected function expectedReverseTransform($input, string $expected): void
    {
        $transformer = new CronExpressionToStringPartsTransformer();
        $value = $transformer->reverseTransform($input);

        $this->assertInstanceOf(CronExpression::class, $value);
        $this->assertSame($expected, $value->getExpression());
    }
}
