<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\TypeGuesser;

use Cron\CronExpression;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

final class CronExpressionTypeGuesser implements FormTypeGuesserInterface
{
    private ?PropertyTypeExtractorInterface $extractor;

    public function __construct(?PropertyTypeExtractorInterface $extractor = null)
    {
        $this->extractor = $extractor ?? $this->createExtractor();
    }
    /**
     * @param string $class
     * @param string $property
     */
    public function guessType($class, $property): ?TypeGuess
    {
        if (!class_exists($class)) {
            return null;
        }


        $types = $this->extractor->getTypes($class, $property);
        if (!$types) {
            return null;
        }
        foreach ($types as $type) {
            if (Type::BUILTIN_TYPE_OBJECT === $type->getBuiltinType() &&
                CronExpression::class === $type->getClassName()) {
                return new TypeGuess(CronExpressionType::class, [], Guess::VERY_HIGH_CONFIDENCE);
            }
        }
        return null;
    }

    /**
     * @param string $class
     * @param string $property
     */
    public function guessRequired($class, $property): ?ValueGuess
    {
        return null;
    }

    /**
     * @param string $class
     * @param string $property
     */
    public function guessMaxLength($class, $property): ?ValueGuess
    {
        return null;
    }

    /**
     * @param string $class
     * @param string $property
     */
    public function guessPattern($class, $property): ?ValueGuess
    {
        return null;
    }

    private function createExtractor(): PropertyTypeExtractorInterface
    {
        return new PropertyInfoExtractor([], [
            new PhpDocExtractor(),
            new ReflectionExtractor(),
        ]);
    }
}
