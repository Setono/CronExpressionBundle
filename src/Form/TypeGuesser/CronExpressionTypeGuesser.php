<?php

declare(strict_types=1);

namespace Setono\CronExpressionBundle\Form\TypeGuesser;

use Cron\CronExpression;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;
use Setono\CronExpressionBundle\Form\Type\CronExpressionType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

final class CronExpressionTypeGuesser implements FormTypeGuesserInterface
{
    private ?PropertyTypeExtractorInterface $extractor;

    public function __construct(?PropertyTypeExtractorInterface $extractor = null)
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

        if ($this->extractor) {
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
        } else {
            // NEXT MAJOR: Remove Compatible Layer
            try {
                $reflectionClass = new \ReflectionClass($class);
                $reflectionProperty = $reflectionClass->getProperty($property);
                if ($reflectionProperty->hasType()) {
                    $type = $reflectionProperty->getType();

                    if ($type->getName() === CronExpression::class) {
                        return new TypeGuess(CronExpressionType::class, [], Guess::VERY_HIGH_CONFIDENCE);
                    }
                } else {
                    $docBlockFactory = DocBlockFactory::createInstance();
                    $contextFactory = new ContextFactory();

                    $docBlock = $docBlockFactory->create($reflectionProperty,
                        $contextFactory->createFromReflector($reflectionProperty));

                    foreach ($docBlock->getTagsByName('var') as $tag) {
                        /**
                         * @var $tag Var_
                         */
                        $tagType = $tag->getType();
                        if ($tagType instanceof Object_) {
                            $fqsen = $tagType->getFqsen();
                            $fqsen = ltrim((string)$fqsen, '\\');
                            if (CronExpression::class === $fqsen) {
                                return new TypeGuess(CronExpressionType::class, [], Guess::VERY_HIGH_CONFIDENCE);
                            }
                        }
                    }
                }
            } catch (\ReflectionException $e) {
                return null;
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
