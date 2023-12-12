<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Tests\Form\TypeGuesser;

use PHPUnit\Framework\TestCase;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Setono\CronExpressionBundle\Form\TypeGuesser\CronExpressionTypeGuesser;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

final class CronExpressionTypeGuesserWithoutPropertyInfoTest extends TestCase
{
    private CronExpressionTypeGuesser $typeGuesser;

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
    public function it_returns_null_if_property_doesnt_exist(): void
    {
        $this->assertNull($this->typeGuesser->guessType(StubWithNoPhpDoc::class, 'property2'));
    }

    /**
     * @test
     */
    public function it_returns_null_if_property_has_wrong_type(): void
    {
        $this->assertNull($this->typeGuesser->guessType(StubWithWrongType::class, 'property'));
    }

    /**
     * @test
     */
    public function it_guesses_type_when_type_is_a_fqcn(): void
    {
        $this->guess_type(StubFqcn::class);
    }

    /**
     * @test
     */
    public function it_guesses_type_when_type_is_an_alias(): void
    {
        $this->guess_type(StubAliased::class);
    }

    /**
     * @test
     */
    public function it_guesses_type_when_type_is_imported(): void
    {
        $this->guess_type(StubImported::class);
    }

    /**
     * @test
     */
    public function it_guesses_type_when_type_is_hinted(): void
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
