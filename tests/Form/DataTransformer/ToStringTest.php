<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Form\DataTransformer\CronExpressionToStringTransformer;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ToStringTest extends TestCase
{
    public function testNullReverseTransform(): void
    {
        $this->expectedReverseTransform(null, '* * * * *');
    }

    public function testInvalidReverseTransform(): void
    {
        $this->invalidReverseTransform(new stdClass());
    }

    public function testInvalidCronReverseTransform(): void
    {
        $this->invalidReverseTransform('* * * * * *');
    }

    protected function invalidReverseTransform(mixed $value): void
    {
        $transformer = new CronExpressionToStringTransformer();
        $this->expectException(TransformationFailedException::class);
        $transformer->reverseTransform($value);
    }

    protected function expectedReverseTransform(mixed $input, string $expected): void
    {
        $transformer = new CronExpressionToStringTransformer();
        $this->assertSame($expected, $transformer->reverseTransform($input)->getExpression());
    }
}
