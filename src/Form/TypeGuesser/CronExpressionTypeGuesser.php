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
use Symfony\Component\PropertyInfo\Type as LegacyType;
use Symfony\Component\TypeInfo\Type;

final class CronExpressionTypeGuesser implements FormTypeGuesserInterface
{
    private PropertyTypeExtractorInterface $extractor;

    public function __construct(?PropertyTypeExtractorInterface $extractor = null)
    {
        $this->extractor = $extractor ?? $this->createExtractor();
    }

    /**
     * @param string $class
     * @param string $property
     */
    #[\Override]
    public function guessType($class, $property): ?TypeGuess
    {
        if (!class_exists($class)) {
            return null;
        }

        if (method_exists($this->extractor, 'getType')) {
            /**
             * @var Type|null $type
             */
            $type = $this->extractor->getType($class, $property);
            if (null === $type) {
                return null;
            }
            if ($type->isIdentifiedBy(CronExpression::class)) {
                return new TypeGuess(CronExpressionType::class, [], Guess::VERY_HIGH_CONFIDENCE);
            }
        } else {
            $types = $this->extractor->getTypes($class, $property);
            if (null === $types) {
                return null;
            }
            foreach ($types as $type) {
                if (LegacyType::BUILTIN_TYPE_OBJECT === $type->getBuiltinType() &&
                    CronExpression::class === $type->getClassName()) {
                    return new TypeGuess(CronExpressionType::class, [], Guess::VERY_HIGH_CONFIDENCE);
                }
            }
        }

        return null;
    }

    /**
     * @param string $class
     * @param string $property
     */
    #[\Override]
    public function guessRequired($class, $property): ?ValueGuess
    {
        return null;
    }

    /**
     * @param string $class
     * @param string $property
     */
    #[\Override]
    public function guessMaxLength($class, $property): ?ValueGuess
    {
        return null;
    }

    /**
     * @param string $class
     * @param string $property
     */
    #[\Override]
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
