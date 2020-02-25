<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\TypeGuesser;

use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Setono\CronExpressionBundle\Form\TypeGuesser\CronExpressionTypeGuesser;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

final class CronExpressionTypeGuesserTest extends TestCase
{
    /** @var CronExpressionTypeGuesser */
    private $typeGuesser;

    public function setUp(): void
    {
        $this->typeGuesser = new CronExpressionTypeGuesser();
    }

    /**
     * @test
     */
    public function it_returns_null_if_class_does_not_exist(): void
    {
        $this->assertNull($this->typeGuesser->guessType('Class\\Does\\Not\\Exist', 'property'));
    }

    /**
     * @test
     */
    public function it_returns_null_if_no_phpdoc_is_present(): void
    {
        $this->assertNull($this->typeGuesser->guessType(StubWithNoPhpDoc::class, 'property'));
    }

    /**
     * @test
     */
    public function it_guesses_type_when_type_is_a_fqcn(): void
    {
        $this->assertCorrectGuess($this->typeGuesser->guessType(StubFqcn::class, 'property'));
    }

    /**
     * @test
     */
    public function it_guesses_type_when_type_is_an_alias(): void
    {
        $this->assertCorrectGuess($this->typeGuesser->guessType(StubAliased::class, 'property'));
    }

    /**
     * @test
     */
    public function it_guesses_type_when_type_is_imported(): void
    {
        $this->assertCorrectGuess($this->typeGuesser->guessType(StubImported::class, 'property'));
    }

    private function assertCorrectGuess(?TypeGuess $res): void
    {
        $this->assertNotNull($res);
        $this->assertSame(CronExpressionType::class, $res->getType());
        $this->assertSame(Guess::VERY_HIGH_CONFIDENCE, $res->getConfidence());
    }
}
