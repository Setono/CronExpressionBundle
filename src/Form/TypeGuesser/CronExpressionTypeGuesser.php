<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\TypeGuesser;

use Brick\Reflection\ImportResolver;
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
     *
     * @return TypeGuess|null
     */
    public function guessType($class, $property)
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

        if (count($propertyTypes) === 0) {
            return null;
        }

        $resolver = new ImportResolver($reflectionClass);

        foreach ($propertyTypes as $propertyType) {
            $fqn = $resolver->resolve($propertyType);

            if (CronExpression::class === $fqn) {
                return new TypeGuess(CronExpressionType::class, [], Guess::VERY_HIGH_CONFIDENCE);
            }
        }

        return null;
    }

    /**
     * @param string $class
     * @param string $property
     *
     * @return ValueGuess|null
     */
    public function guessRequired($class, $property)
    {
        return null;
    }

    /**
     * @param string $class
     * @param string $property
     *
     * @return ValueGuess|null
     */
    public function guessMaxLength($class, $property)
    {
        return null;
    }

    /**
     * @param string $class
     * @param string $property
     *
     * @return ValueGuess|null
     */
    public function guessPattern($class, $property)
    {
        return null;
    }
}
