<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\TypeGuesser;

use Cron\CronExpression;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

final class CronExpressionTypeGuesser implements FormTypeGuesserInterface
{
    protected PropertyTypeExtractorInterface $extractor;

    public function __construct(PropertyTypeExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
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
}
