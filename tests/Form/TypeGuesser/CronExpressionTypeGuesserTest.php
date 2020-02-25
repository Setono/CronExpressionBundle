<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\TypeGuesser;

use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Setono\CronExpressionBundle\Form\TypeGuesser\CronExpressionTypeGuesser;
use Symfony\Component\Form\Guess\Guess;

final class CronExpressionTypeGuesserTest extends TestCase
{
    /**
     * @test
     */
    public function guessType(): void
    {
        $guesser = new CronExpressionTypeGuesser();
        $res = $guesser->guessType(Stub::class, 'property');

        $this->assertNotNull($res);
        $this->assertSame(CronExpressionType::class, $res->getType());
        $this->assertSame(Guess::VERY_HIGH_CONFIDENCE, $res->getConfidence());

        $guesser = new CronExpressionTypeGuesser();
        $res = $guesser->guessType(StubAliased::class, 'property');

        $this->assertNotNull($res);
        $this->assertSame(CronExpressionType::class, $res->getType());
        $this->assertSame(Guess::VERY_HIGH_CONFIDENCE, $res->getConfidence());

        $guesser = new CronExpressionTypeGuesser();
        $res = $guesser->guessType(StubImported::class, 'property');

        $this->assertNotNull($res);
        $this->assertSame(CronExpressionType::class, $res->getType());
        $this->assertSame(Guess::VERY_HIGH_CONFIDENCE, $res->getConfidence());
    }
}
