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
    private CronExpressionTypeGuesser $typeGuesser;

    #[\Override]
    public function setUp(): void
    {
        $this->typeGuesser = new CronExpressionTypeGuesser();
    }

    public function testItReturnsNullIfClassDoesNotExist(): void
    {
        $this->assertNull($this->typeGuesser->guessType('Class\\Does\\Not\\Exist', 'property'));
    }

    public function testItReturnsNullIfNoPhpdocIsPresent(): void
    {
        $this->assertNull($this->typeGuesser->guessType(StubWithNoPhpDoc::class, 'property'));
    }

    public function testItReturnsNullIfPropertyDoesntExist(): void
    {
        $this->assertNull($this->typeGuesser->guessType(StubWithNoPhpDoc::class, 'property2'));
    }

    public function testItReturnsNullIfPropertyHasWrongType(): void
    {
        $this->assertNull($this->typeGuesser->guessType(StubWithWrongType::class, 'property'));
    }

    public function testItGuessesTypeWhenTypeIsAFqcn(): void
    {
        $this->guess_type(StubFqcn::class);
    }

    public function testItGuessesTypeWhenTypeIsAnAlias(): void
    {
        $this->guess_type(StubAliased::class);
    }

    public function testItGuessesTypeWhenTypeIsImported(): void
    {
        $this->guess_type(StubImported::class);
    }

    public function testItGuessesTypeWhenTypeIsHinted(): void
    {
        $this->guess_type(StubWithTypeHint::class);
    }

    protected function guess_type(string $class): void
    {
        $this->assertCorrectGuess($this->typeGuesser->guessType($class, 'property'));
        $this->assertNull($this->typeGuesser->guessRequired($class, 'property'));
        $this->assertNull($this->typeGuesser->guessMaxLength($class, 'property'));
        $this->assertNull($this->typeGuesser->guessPattern($class, 'property'));
    }

    private function assertCorrectGuess(?TypeGuess $res): void
    {
        $this->assertNotNull($res);
        $this->assertSame(CronExpressionType::class, $res->getType());
        $this->assertSame(Guess::VERY_HIGH_CONFIDENCE, $res->getConfidence());
    }
}
