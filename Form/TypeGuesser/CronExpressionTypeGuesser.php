<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\TypeGuesser;

use Brick\Reflection\ImportResolver;
use Cron\CronExpression;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

final class CronExpressionTypeGuesser implements FormTypeGuesserInterface
{
    public function guessType($class, $property)
    {
        try {
            $reflectionClass = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            return null;
        }

        $reflectionProperty = $reflectionClass->getProperty($property);

        if (is_bool($reflectionProperty->getDocComment())) {
            return null;
        }

        $factory = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
        $docBlock = $factory->create($reflectionProperty->getDocComment());
        $varTags = $docBlock->getTagsByName('var');

        if (empty($varTags)) {
            return null;
        }

        /** @var Var_ $varTag */
        $varTag = $varTags[0];

        $typeName = (string) $varTag->getType();

        $resolver = new ImportResolver($reflectionClass);

        foreach ([$typeName, ltrim($typeName, '\\')] as $item) {
            $fqn = $resolver->resolve($item);

            if (CronExpression::class === $fqn) {
                return new TypeGuess(CronExpressionType::class, [], Guess::VERY_HIGH_CONFIDENCE);
            }
        }

        return null;
    }

    public function guessRequired($class, $property)
    {
        return null;
    }

    public function guessMaxLength($class, $property)
    {
        return null;
    }

    public function guessPattern($class, $property)
    {
        return null;
    }
}
