<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\TypeGuesser;

use Brick\Reflection\ReflectionTools;
use Cron\CronExpression;
use ReflectionClass;
use ReflectionException;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;

final class CronExpressionTypeGuesser implements FormTypeGuesserInterface
{
    /**
     * @param string $class
     * @param string $property
     */
    public function guessType($class, $property): ?TypeGuess
    {
        if (!class_exists($class)) {
            return null;
        }

        try {
            $reflectionClass = new ReflectionClass($class);
            $reflectionProperty = $reflectionClass->getProperty($property);
        } catch (ReflectionException $e) {
            return null;
        }

        $reflectionTools = new ReflectionTools();
        $propertyTypes = $reflectionTools->getPropertyTypes($reflectionProperty);

        if (in_array(CronExpression::class, $propertyTypes, true)) {
            return new TypeGuess(CronExpressionType::class, [], Guess::VERY_HIGH_CONFIDENCE);
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
