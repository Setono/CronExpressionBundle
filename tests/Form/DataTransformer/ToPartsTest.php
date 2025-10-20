<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Form\DataTransformer\CronExpressionToPartsTransformer;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ToPartsTest extends TestCase
{
    public function testNullReverseTransform(): void
    {
        $this->expectedReverseTransform(null, '* * * * *');
    }

    public function testArrayReverseTransform(): void
    {
        $this->expectedReverseTransform([
            'minutes' => ['0'],
            'hours' => ['12'],
            'days' => ['1'],
            'months' => ['6'],
            'weekdays' => ['3'],
        ], '0 12 1 6 3');
    }

    public function testWrongClassInvalidReverseTransform(): void
    {
        $this->invalidReverseTransform(new stdClass());
    }

    public function testMissingKeyInvalidReverseTransform(): void
    {
        $this->invalidReverseTransform([
            'minutes' => ['0'],
        ]);
    }

    public function testFaultyKeyTypeInvalidReverseTransform(): void
    {
        $this->invalidReverseTransform([
            'minutes' => '0',
            'hours' => ['12'],
            'days' => ['1'],
            'months' => ['6'],
            'weekdays' => ['3'],
        ]);
    }

    public function testFaultyKeyRangeInvalidReverseTransform(): void
    {
        $this->invalidReverseTransform([
            'minutes' => ['61'],
            'hours' => ['12'],
            'days' => ['1'],
            'months' => ['6'],
            'weekdays' => ['3'],
        ]);
    }

    protected function invalidReverseTransform(mixed $value): void
    {
        $transformer = new CronExpressionToPartsTransformer();
        $this->expectException(TransformationFailedException::class);
        $transformer->reverseTransform($value);
    }

    protected function expectedReverseTransform(mixed $input, string $expected): void
    {
        $transformer = new CronExpressionToPartsTransformer();
        $this->assertSame($expected, $transformer->reverseTransform($input)->getExpression());
    }
}
