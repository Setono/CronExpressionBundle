<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\DataTransformer;

use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Form\DataTransformer\CronExpressionToStringTransformer;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ToStringTest extends TestCase
{
    public function testInvalidReverseTransform(): void
    {
        $transformer = new CronExpressionToStringTransformer();
        $this->expectException(TransformationFailedException::class);
        $transformer->reverseTransform(new stdClass());
    }
}
